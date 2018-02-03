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
#include "hphp/runtime/vm/vm-regs.h"
#include "hphp/runtime/ext/collections/ext_collections.h"

#include "../lib/libsass/include/sass.h"

#include "CallbackBridge.h"
#include "CustomImporterBridge.h"

namespace HPHP {

const StaticString s_ExceptionMessage_invalidImporterEntry("Returned value must be an instance of Traversable<?SassImport> or null.");
const StaticString s_Type_SassImport_className("Sass\\SassImport");
const StaticString s_Type_SassImportMethod_finalize("finalize");

Array CustomImporterBridge::pre_process_args(std::vector<void*> in) const {
  Array argv;
  for (void* value : in) {
    argv.append(String((char const*)value));
  }
  return argv;
}

Sass_Import_List CustomImporterBridge::makeError(const char* message) const {
  Sass_Import_List imports = sass_make_import_list(1);
  imports[0] = sass_make_import_entry(0, 0, 0);
  sass_import_set_error(imports[0], message, -1, -1);
  return imports;
}

ArrayIter CustomImporterBridge::get_arrayiter_helper(const Variant& v, size_t& sz) const {
  if (v.isArray()) {
    ArrayData* ad = v.getArrayData();
    sz = ad->size();
    return ArrayIter(ad);
  }
  if (v.isObject()) {
    ObjectData* obj = v.getObjectData();
    if (obj->isCollection()) {
      sz = collections::getSize(obj);
      return ArrayIter(obj);
    }
  }
  SystemLib::throwExceptionObject("");
}

Sass_Import* CustomImporterBridge::get_importer_entry(const Variant& val) const {
  if (UNLIKELY(val.isNull())) {
    return sass_make_import_entry(0, 0, 0);
  }
  if (UNLIKELY(!val.isObject())) {
    auto entry = sass_make_import_entry(0, 0, 0);
    sass_import_set_error(entry, s_ExceptionMessage_invalidImporterEntry.c_str(), -1, -1);
    return entry;
  }

  const Object obj = val.asCObjRef();
  if (UNLIKELY(!obj->instanceof(s_Type_SassImport_className))) {
    auto entry = sass_make_import_entry(0, 0, 0);
    sass_import_set_error(entry, s_ExceptionMessage_invalidImporterEntry.c_str(), -1, -1);
    return entry;
  }

  const Func* func = obj->getVMClass()->lookupMethod(s_Type_SassImportMethod_finalize.get());

  tvDecRefGen(g_context->invokeFunc(func, init_null_variant, obj.get()));

  Variant source = obj->o_get("source", true, s_Type_SassImport_className);
  Variant srcmap = obj->o_get("srcmap", true, s_Type_SassImport_className);

  auto entry = sass_make_import_entry(
    obj->o_get("path", true, s_Type_SassImport_className).toString().c_str(),
    // The memory passed with source and srcmap is taken over by LibSass and freed automatically when the import is done.
    source.isNull() ? 0 : strdup(source.toString().c_str()),
    srcmap.isNull() ? 0 : strdup(srcmap.toString().c_str())
  );

  return entry;
}

Sass_Import_List CustomImporterBridge::post_process_return_value(const Variant& val) const {
  Sass_Import_List imports = 0;

  imports = sass_make_import_list(1);
  imports[0] = sass_make_import_entry(0, 0, 0);

  if (val.isNull()) {
    return NULL;

  } else {
    ArrayIter iter;
    size_t size;
    try {
      iter = get_arrayiter_helper(val, size);
    } catch (const Object& o) {
      Sass_Import_List imports = sass_make_import_list(1);
      imports[0] = this->get_importer_entry(val);
      return imports;
    }

    Sass_Import_List imports = sass_make_import_list(size);
    for (; iter; ++iter) {
      imports[iter.getPos()] = this->get_importer_entry(iter.second());
    }
    return imports;
  }
}

}
