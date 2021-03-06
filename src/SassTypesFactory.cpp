/*
   +----------------------------------------------------------------------+
   | Integration of libsass for HHVM                                      |
   +----------------------------------------------------------------------+
   | Copyright (c) 2015 - 2017 Alexander Papst                            |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
*/

#include "hphp/runtime/ext/extension.h"
#include "hphp/runtime/vm/native-data.h"

#include "hphp/runtime/base/array-init.h"
#include "hphp/runtime/base/builtin-functions.h"
#include "hphp/runtime/vm/vm-regs.h"

#include "../lib/libsass/include/sass.h"

#include "SassTypesFactory.h"


#include "hphp/runtime/ext/std/ext_std_variable.h"

namespace HPHP {

const StaticString s_Type_SassNull_className("Sass\\Types\\SassNull");
const StaticString s_Type_SassNumber_className("Sass\\Types\\SassNumber");
const StaticString s_Type_SassString_className("Sass\\Types\\SassString");
const StaticString s_Type_SassBoolean_className("Sass\\Types\\SassBoolean");
const StaticString s_Type_SassColor_className("Sass\\Types\\SassColor");
const StaticString s_Type_SassList_className("Sass\\Types\\SassList");
const StaticString s_Type_SassMapPair_className("Sass\\Types\\SassMapPair");
const StaticString s_Type_SassMap_className("Sass\\Types\\SassMap");
const StaticString s_Type_SassWarning_className("Sass\\Types\\SassWarning");
const StaticString s_Type_SassError_className("Sass\\Types\\SassError");

const StaticString s_Type_SassValueMethod_setValue("setValue");
const StaticString s_Type_SassValueMethod_setRGB("setRGB");
const StaticString s_Type_SassValueMethod_setMessage("setMessage");
const StaticString s_Type_SassValueMethod_addAll("addAll");
const StaticString s_Type_SassValueMethod_setSeparator("setSeparator");
const StaticString s_Type_SassValueMethod_setIsBracketed("setIsBracketed");
const StaticString s_Type_SassValueMethod_set("set");
const StaticString s_Type_SassValueMethod_isValid("isValid");

const StaticString s_ExceptionMessage_circularReference("SassList and SassMap do not support circular references.");
const StaticString s_RuntimeExceptionClass("RuntimeException");

Object SassTypesFactory::php_create_and_construct(StaticString classname) {
  static Class* c_class;

  c_class = Unit::lookupClass(classname.get());
  assert(c_class);
  return Object{c_class};
}

Variant SassTypesFactory::to_php_null() {
  return php_create_and_construct(s_Type_SassNull_className);
}

Variant SassTypesFactory::to_php_number(Sass_Value* v) {
  Object number = php_create_and_construct(s_Type_SassNumber_className);

  const Func* func = number->getVMClass()->lookupMethod(s_Type_SassValueMethod_setValue.get());

  tvDecRefGen(g_context->invokeFunc(
    func,
    make_packed_array(
      sass_number_get_value(v),
      String(sass_number_get_unit(v))
    ),
    number.get()
  ));

  return number;
}

Variant SassTypesFactory::to_php_string(Sass_Value* v) {
  Object str = php_create_and_construct(s_Type_SassString_className);

  const Func* func = str->getVMClass()->lookupMethod(s_Type_SassValueMethod_setValue.get());

  tvDecRefGen(g_context->invokeFunc(
    func,
    make_packed_array(
      String(sass_string_get_value(v))
    ),
    str.get()
  ));

  if (sass_string_is_quoted(v)) {
    tvDecRefGen(g_context->invokeFunc(
      str->getVMClass()->lookupMethod(StaticString("quote").get()),
      init_null_variant,
      str.get()
    ));
  }

  return str;
}

Variant SassTypesFactory::to_php_boolean(Sass_Value* v) {
  Object boolean = php_create_and_construct(s_Type_SassBoolean_className);

  const Func* func = boolean->getVMClass()->lookupMethod(s_Type_SassValueMethod_setValue.get());

  tvDecRefGen(g_context->invokeFunc(
    func,
    make_packed_array(sass_boolean_get_value(v)),
    boolean.get()
  ));

  return boolean;
}

Variant SassTypesFactory::to_php_color(Sass_Value* v) {
  Object color = php_create_and_construct(s_Type_SassColor_className);

  const Func* func = color->getVMClass()->lookupMethod(s_Type_SassValueMethod_setRGB.get());

  tvDecRefGen(g_context->invokeFunc(
    func,
    make_packed_array(
      static_cast<std::int64_t>(std::round(sass_color_get_r(v))),
      static_cast<std::int64_t>(std::round(sass_color_get_g(v))),
      static_cast<std::int64_t>(std::round(sass_color_get_b(v))),
      sass_color_get_a(v)
    ),
    color.get()
  ));

  return color;
}

Variant SassTypesFactory::to_php_list(Sass_Value* v) {
  Array data;
  Object list = php_create_and_construct(s_Type_SassList_className);

  const Func* funcSetSeparator = list->getVMClass()->lookupMethod(s_Type_SassValueMethod_setSeparator.get());
  const Func* funcSetIsBracketed = list->getVMClass()->lookupMethod(s_Type_SassValueMethod_setIsBracketed.get());
  const Func* funcAddAll = list->getVMClass()->lookupMethod(s_Type_SassValueMethod_addAll.get());

  int64_t i = 0;
  int64_t n = sass_list_get_length(v);

  for (; i < n; ++i) {
    data.append(to_php(sass_list_get_value(v, i)));
  }

  tvDecRefGen(g_context->invokeFunc(
    funcSetSeparator,
    make_packed_array(String(SASS_COMMA == sass_list_get_separator(v) ? "," : " ")),
    list.get()
  ));

  tvDecRefGen(g_context->invokeFunc(
    funcSetIsBracketed,
    make_packed_array(sass_list_get_is_bracketed(v)),
    list.get()
  ));

  tvDecRefGen(g_context->invokeFunc(funcAddAll, make_packed_array(data), list.get()));

  return list;
}

Variant SassTypesFactory::to_php_map(Sass_Value* v) {
  Object map = php_create_and_construct(s_Type_SassMap_className);
  const Func* funcSet = map->getVMClass()->lookupMethod(s_Type_SassValueMethod_set.get());

  int64_t i = 0;
  int64_t n = sass_map_get_length(v);

  for (; i < n; ++i) {
    tvDecRefGen(g_context->invokeFunc(
      funcSet,
      PackedArrayInit(2)
         .append(to_php(sass_map_get_key(v, i)))
         .append(to_php(sass_map_get_value(v, i)))
         .toArray(),
      map.get()
    ));
  }

  return map;
}

Variant SassTypesFactory::to_php_warning(Sass_Value* v) {
  Object warning = php_create_and_construct(s_Type_SassWarning_className);

  const Func* func = warning->getVMClass()->lookupMethod(s_Type_SassValueMethod_setMessage.get());

  tvDecRefGen(g_context->invokeFunc(
    func,
    make_packed_array(String(sass_warning_get_message(v))),
    warning.get()
  ));

  return warning;
}

Variant SassTypesFactory::to_php_error(Sass_Value* v) {
  Object error = php_create_and_construct(s_Type_SassError_className);

  const Func* func = error->getVMClass()->lookupMethod(s_Type_SassValueMethod_setMessage.get());

  tvDecRefGen(g_context->invokeFunc(
    func,
    make_packed_array(String(sass_error_get_message(v))),
    error.get()
  ));

  return error;
}

Variant SassTypesFactory::to_php(Sass_Value* v) {
  switch (sass_value_get_tag(v)) {
    case SASS_NULL: {
      return to_php_null();
    }
    case SASS_NUMBER: {
      return to_php_number(v);
    }
    case SASS_STRING: {
      return to_php_string(v);
    }
    case SASS_BOOLEAN: {
      return to_php_boolean(v);
    }
    case SASS_COLOR: {
      return to_php_color(v);
    }
    case SASS_LIST: {
      return to_php_list(v);
    }
    case SASS_MAP: {
      return to_php_map(v);
    }
    case SASS_WARNING: {
      return to_php_warning(v);
    }
    case SASS_ERROR: {
      return to_php_error(v);
    }
  }
  throw_object(
    s_RuntimeExceptionClass,
    make_packed_array(
      String("Unknown sass value can not be converted to php class."),
      54551061
    )
  );
}

union Sass_Value* SassTypesFactory::to_sass_null() {
  return sass_make_null();
}

union Sass_Value* SassTypesFactory::to_sass_number(const Object& val) {
  return sass_make_number(
    val->o_get("value", true, s_Type_SassNumber_className).toDouble(),
    val->o_get("unit", true, s_Type_SassNumber_className).toString().c_str()
  );
}

union Sass_Value* SassTypesFactory::to_sass_string(const Object& val) {
  if (val->o_get("isQuoted", true, s_Type_SassString_className).toBooleanVal()) {
    return sass_make_qstring(
      val->o_get("value", true, s_Type_SassString_className).toString().c_str()
    );
  } else {
    return sass_make_string(
      val->o_get("value", true, s_Type_SassString_className).toString().c_str()
    );
  }
}

union Sass_Value* SassTypesFactory::to_sass_boolean(const Object& val) {
  return sass_make_boolean(
    val->o_get("value", true, s_Type_SassBoolean_className).toBooleanVal()
  );
}

union Sass_Value* SassTypesFactory::to_sass_color(const Object& val) {
  return sass_make_color(
    val->o_get("r", true, s_Type_SassColor_className).toDouble(),
    val->o_get("g", true, s_Type_SassColor_className).toDouble(),
    val->o_get("b", true, s_Type_SassColor_className).toDouble(),
    val->o_get("alpha", true, s_Type_SassColor_className).toDouble()
  );
}

union Sass_Value* SassTypesFactory::to_sass_list(const Object& val) {
  Sass_Separator sep = SASS_COMMA;
  bool is_bracketed = val->o_get("isBracketed", true, s_Type_SassList_className).toBooleanVal();

  if (val->o_get("separator", true, s_Type_SassList_className).toString().equal(String(" "))) {
    sep = SASS_SPACE;
  }

  const Func* func = val->getVMClass()->lookupMethod(s_Type_SassValueMethod_isValid.get());
  auto ret = Variant::attach(
    g_context->invokeFunc(func, init_null_variant, val.get())
  );
  if (!ret.isBoolean() || !ret.toBoolean()) {
    throw_object(
      s_RuntimeExceptionClass,
      make_packed_array(s_ExceptionMessage_circularReference.get(), 54551060)
    );
  }

  Array phplist = val->o_get("list", true, s_Type_SassList_className).toArray();

  union Sass_Value* sasslist = sass_make_list(phplist.size(), sep, is_bracketed);

  for (ArrayIter iter(phplist); iter; ++iter) {
    sass_list_set_value(sasslist, iter.getPos(), to_sass(iter.secondRef()));
  }

  return sasslist;
}

union Sass_Value* SassTypesFactory::to_sass_map(const Object& val) {
  const Func* func = val->getVMClass()->lookupMethod(s_Type_SassValueMethod_isValid.get());
  auto ret = Variant::attach(
    g_context->invokeFunc(func, init_null_variant, val.get())
  );
  if (!ret.isBoolean() || !ret.toBoolean()) {
    throw_object(
      s_RuntimeExceptionClass,
      make_packed_array(s_ExceptionMessage_circularReference.get(), 54551060)
    );
  }

  Array phpmap = val->o_get("map", true, s_Type_SassMap_className).toArray();

  union Sass_Value* map = sass_make_map(phpmap.size());

  for (ArrayIter iter(phpmap); iter; ++iter) {
    const Object pair = iter.secondRef().asCObjRef();
    sass_map_set_key(map, iter.getPos(), to_sass(pair->o_get("k", true, s_Type_SassMapPair_className)));
    sass_map_set_value(map, iter.getPos(), to_sass(pair->o_get("v", true, s_Type_SassMapPair_className)));
  }
  return map;
}

union Sass_Value* SassTypesFactory::to_sass_warning(const Object& val) {
  return sass_make_warning(
    val->o_get("message", true, s_Type_SassWarning_className).toString().c_str()
  );
}

union Sass_Value* SassTypesFactory::to_sass_error(const Object& val) {
  return sass_make_error(
    val->o_get("message", true, s_Type_SassError_className).toString().c_str()
  );
}

union Sass_Value* SassTypesFactory::to_sass(const Variant& val) {
  if (LIKELY(val.isObject())) {
    const Object obj = val.asCObjRef();
    if (obj->instanceof(s_Type_SassNull_className)) {
      return to_sass_null();

    } else if (obj->instanceof(s_Type_SassNumber_className)) {
      return to_sass_number(obj);

    } else if (obj->instanceof(s_Type_SassString_className)) {
      return to_sass_string(obj);

    } else if (obj->instanceof(s_Type_SassBoolean_className)) {
      return to_sass_boolean(obj);

    } else if (obj->instanceof(s_Type_SassColor_className)) {
      return to_sass_color(obj);

    } else if (obj->instanceof(s_Type_SassList_className)) {
      return to_sass_list(obj);

    } else if (obj->instanceof(s_Type_SassMap_className)) {
      return to_sass_map(obj);

    } else if (obj->instanceof(s_Type_SassWarning_className)) {
      return to_sass_warning(obj);

    } else if (obj->instanceof(s_Type_SassError_className)) {
      return to_sass_error(obj);

    }
  } else if (val.isNull()) {
    return to_sass_null();
  }

  String msg("Expected a supported instance of Sass\\Types\\SassValue, got ");
  if (val.isObject()) {
    msg += "instance of ";
    msg += val.toObject()->getClassName().c_str();
  } else {
    msg += getDataTypeString(val.getType()).c_str();
  }
  msg += ".";

  throw_object(
    s_RuntimeExceptionClass,
    make_packed_array(msg, 54551062)
  );
}

}
