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
#include "hphp/runtime/base/array-init.h"
#include "hphp/runtime/base/string-util.h"
#include "hphp/runtime/base/builtin-functions.h"

#include "hphp/runtime/ext/std/ext_std_variable.h"

#include "hphp/runtime/vm/vm-regs.h"
#include "hphp/runtime/vm/native-data.h"

#include "lib/libsass/include/sass.h"

#include "src/SassTypesFactory.h"
#include "src/ExceptionManager.h"
#include "src/CallbackBridge.h"
#include "src/CustomFunctionBridge.h"
#include "src/CustomImporterBridge.h"

namespace HPHP {

const StaticString s_Sass("Sass\\Sass");

const StaticString s_STYLE_NESTED("STYLE_NESTED");
const StaticString s_STYLE_EXPANDED("STYLE_EXPANDED");
const StaticString s_STYLE_COMPACT("STYLE_COMPACT");
const StaticString s_STYLE_COMPRESSED("STYLE_COMPRESSED");

const StaticString s_SYNTAX_SCSS("SYNTAX_SCSS");
const StaticString s_SYNTAX_SASS("SYNTAX_SASS");
const int64_t i_SYNTAX_SCSS = 1;
const int64_t i_SYNTAX_SASS = 2;

const StaticString s_SASS2SCSS_PRETTIFY_0("SASS2SCSS_PRETTIFY_0");
const StaticString s_SASS2SCSS_PRETTIFY_1("SASS2SCSS_PRETTIFY_1");
const StaticString s_SASS2SCSS_PRETTIFY_2("SASS2SCSS_PRETTIFY_2");
const StaticString s_SASS2SCSS_PRETTIFY_3("SASS2SCSS_PRETTIFY_3");
const StaticString s_SASS2SCSS_KEEP_COMMENT("SASS2SCSS_KEEP_COMMENT");
const StaticString s_SASS2SCSS_STRIP_COMMENT("SASS2SCSS_STRIP_COMMENT");
const StaticString s_SASS2SCSS_CONVERT_COMMENT("SASS2SCSS_CONVERT_COMMENT");

const StaticString s_SassResponse_css("css");
const StaticString s_SassResponse_map("map");

const StaticString s_SassImporter_callback("callback");
const StaticString s_SassImporter_priority("priority");
const StaticString s_SassHeader_content("content");

#ifdef __WIN__
const StaticString s_Glue(";");
#else
const StaticString s_Glue(",");
#endif


struct Sass_Context_Wrapper {
  Sass_Data_Context* dctx;
  Sass_File_Context* fctx;
  Sass_Context* ctx;
};

struct Sass_Context_Wrapper* sass_make_context_wrapper() {
  return (Sass_Context_Wrapper*)calloc(1, sizeof(Sass_Context_Wrapper));
}

struct Sass_Context_Wrapper* sass_make_data_context_wrapper(char* source_string) {
  struct Sass_Context_Wrapper* ctx_w = sass_make_context_wrapper();
  ctx_w->dctx = sass_make_data_context(source_string);
  ctx_w->ctx = sass_data_context_get_context(ctx_w->dctx);
  return ctx_w;
}

struct Sass_Context_Wrapper* sass_make_file_context_wrapper(const char* input_path) {
  struct Sass_Context_Wrapper* ctx_w = sass_make_context_wrapper();
  ctx_w->fctx = sass_make_file_context(input_path);
  ctx_w->ctx = sass_file_context_get_context(ctx_w->fctx);
  return ctx_w;
}

union Sass_Value* sass_custom_function(const union Sass_Value* s_args,
                                       Sass_Function_Entry cb,
                                       struct Sass_Compiler* comp) {
  void* cookie = sass_function_get_cookie(cb);
  CustomFunctionBridge& bridge = *(static_cast<CustomFunctionBridge*>(cookie));

  std::vector<void*> argv;
  for (unsigned l = sass_list_get_length(s_args), i = 0; i < l; i++) {
    argv.push_back((void*)sass_list_get_value(s_args, i));
  }

  return bridge(argv);
}

Sass_Import_List sass_custom_importer(const char* cur_path,
                               Sass_Importer_Entry cb,
                               struct Sass_Compiler* comp) {

  void* cookie = sass_importer_get_cookie(cb);
  struct Sass_Import* previous = sass_compiler_get_last_import(comp);
  const char* prev_path = sass_import_get_abs_path(previous);
  CustomImporterBridge& bridge = *(static_cast<CustomImporterBridge*>(cookie));

  std::vector<void*> argv;
  argv.push_back((void*)cur_path);
  argv.push_back((void*)prev_path);

  return bridge(argv);
}

// create a custom header to define to variables
Sass_Import_List custom_header(const char* cur_path, Sass_Importer_Entry cb, struct Sass_Compiler* comp)
{
  const char* header = static_cast<const char*>(sass_importer_get_cookie(cb));

  // create a list to hold our import entries
  Sass_Import_List incs = sass_make_import_list(1);
  // create our only import entry (must make copy)
  incs[0] = sass_make_import_entry("[header]", strdup(header), 0);
  // return imports
  return incs;
}

static void set_sass_options(ObjectData* obj, struct Sass_Options* opts) {

  Array fnCallbacks = obj->o_get("userFunctions", true, s_Sass).toArray();
  if (!fnCallbacks.empty()) {
    Sass_Function_List fn_list = sass_make_function_list(fnCallbacks.length());

    int64_t i = 0;
    for (ArrayIter fn_iter(fnCallbacks); fn_iter; ++fn_iter, ++i) {
      String signature(fn_iter.first().toString());
      Variant callback = fn_iter.secondRefPlus();

      CustomFunctionBridge *bridge = new CustomFunctionBridge(callback);

      sass_function_set_list_entry(
        fn_list,
        i,
        sass_make_function(signature.c_str(), sass_custom_function, bridge)
      );
    }

    sass_option_set_c_functions(opts, fn_list);
  }

  Array fnImportCallbacks = obj->o_get("userImporters", true, s_Sass).toArray();
  if (!fnImportCallbacks.empty()) {
    auto callback_length = fnImportCallbacks.length();
    Sass_Importer_List fn_importer_list = sass_make_importer_list(callback_length);

    int64_t i = 0;
    for (ArrayIter fn_iter(fnImportCallbacks); fn_iter; ++fn_iter, ++i) {
      Variant v = fn_iter.secondRefPlus();
      if (!v.isArray()) {
        continue;
      }
      Array a = v.toArray();
      if (!a.exists(s_SassImporter_callback)
        || !a.exists(s_SassImporter_priority)
      ) {
        continue;
      }
      CustomImporterBridge *bridge = new CustomImporterBridge(
        a[s_SassImporter_callback]
      );

      fn_importer_list[i] = sass_make_importer(
        sass_custom_importer,
        a[s_SassImporter_priority].toDouble(),
        bridge
      );
    }

    sass_option_set_c_importers(opts, fn_importer_list);
  }

  Array headers = obj->o_get("headers", true, s_Sass).toArray();
  if (!headers.empty()) {
    headers.sort([](const Variant& v1, const Variant& v2, const void *data) -> int {
      int64_t vi1 = v1.toArray()[s_SassImporter_priority].toInt64();
      int64_t vi2 = v2.toArray()[s_SassImporter_priority].toInt64();
      return (vi1 == vi2) ? 0 : ((vi1 < vi2) ? 1 : -1);
    }, false, false, nullptr);
    String header;
    for (ArrayIter iter(headers); iter; ++iter) {
      header += iter.secondRef().toArray()[s_SassHeader_content].toString()+"\n";
    }

    // allocate a custom function caller
    Sass_Importer_Entry c_header = sass_make_importer(
      custom_header,
      5000, // There is just one, so meh.
      (void*)header.c_str()
    );

    // create list of all custom functions
    Sass_Importer_List c_headers = sass_make_importer_list(1);
    if (!c_headers) {
      throw ExceptionManager::createSassException(
        String::FromCStr("Couldn't allocate enough memory for the custom header."),
        String::FromCStr(""),
        54550001
      );
    }
    // put the only function in this plugin to the list
    sass_importer_set_list_entry(c_headers, 0, c_header);
    // return the list
    sass_option_set_c_headers(opts, c_headers);
  }

  // All options have been validated in ext_sass.php
  sass_option_set_precision(opts, obj->o_get("precision", true, s_Sass).toInt64Val());

  sass_option_set_output_style(
    opts,
    (Sass_Output_Style)obj->o_get("style", true, s_Sass).toInt64Val()
  );

  Array includePaths = obj->o_get("includePaths", true, s_Sass).toCArrRef();
  if (!includePaths.empty()) {
    sass_option_set_include_path(
      opts,
      StringUtil::Implode(includePaths, s_Glue).c_str()
    );
  }

  bool includeSourceComments = obj->o_get("sourceComments", true, s_Sass).toBooleanVal();
  sass_option_set_source_comments(opts, includeSourceComments);
  if (includeSourceComments) {
    sass_option_set_omit_source_map_url(opts, false);
  }

  sass_option_set_is_indented_syntax_src(
    opts,
    obj->o_get("syntax", true, s_Sass).toInt64Val() == i_SYNTAX_SASS
  );

  if (!obj->o_get("linefeed", true, s_Sass).isNull()) {
    sass_option_set_linefeed(
      opts,
      obj->o_get("linefeed", true, s_Sass).toString().c_str()
    );
  }
  if (!obj->o_get("indent", true, s_Sass).isNull()) {
    sass_option_set_indent(
      opts,
      obj->o_get("indent", true, s_Sass).toString().c_str()
    );
  }

  sass_option_set_source_map_embed(
    opts,
    obj->o_get("embedMap", true, s_Sass).toBooleanVal()
  );

  if (!obj->o_get("sourceRoot", true, s_Sass).isNull()) {
    sass_option_set_source_map_root(
      opts,
      obj->o_get("sourceRoot", true, s_Sass).toString().c_str()
    );
  }
}

void sass_free_context_wrapper(struct Sass_Context_Wrapper* ctx_w) {
  if (ctx_w->ctx) {
    struct Sass_Options* opts = sass_context_get_options(ctx_w->ctx);

    Sass_Function_List fnlist = sass_option_get_c_functions(opts);
    if (fnlist) {
      while (fnlist && *fnlist) {
        void* cookie = sass_function_get_cookie(*fnlist);
        CustomFunctionBridge* bridge = (static_cast<CustomFunctionBridge*>(cookie));
        delete bridge;
        ++fnlist;
      }
    }
    Sass_Importer_List importers = sass_option_get_c_importers(opts);
    if (importers) {
      while (importers && *importers) {
        void* cookie = sass_importer_get_cookie(*importers);
        CustomImporterBridge* bridge = (static_cast<CustomImporterBridge*>(cookie));
        delete bridge;
        ++importers;
      }
    }
  }

  if (ctx_w->dctx) {
    sass_delete_data_context(ctx_w->dctx);
  } else if (ctx_w->fctx) {
      sass_delete_file_context(ctx_w->fctx);
  }
}

static Array compileScss(ObjectData* obj, struct Sass_Context_Wrapper* ctx_w) {
  struct Sass_Options* opts;

  int64_t status;

  if (ctx_w->fctx) {
    ctx_w->ctx = sass_file_context_get_context(ctx_w->fctx);
  } else {
    ctx_w->ctx = sass_data_context_get_context(ctx_w->dctx);
  }
  opts = sass_context_get_options(ctx_w->ctx);

  set_sass_options(obj, opts);

  if (sass_option_get_source_map_file(opts) != 0) {
    sass_option_set_omit_source_map_url(opts, false);
    sass_option_set_source_map_contents(opts, true);
  }

  if (ctx_w->fctx) {
    status = sass_compile_file_context(ctx_w->fctx);
  } else {
    status = sass_compile_data_context(ctx_w->dctx);
  }

  // Check the context for any errors...
  if (status != 0) {
    String exMsg = String::FromCStr(sass_context_get_error_message(ctx_w->ctx));
    String exJson = String::FromCStr(sass_context_get_error_json(ctx_w->ctx));
    sass_free_context_wrapper(ctx_w);

    throw ExceptionManager::createSassException(exMsg, exJson, status);
  }

  Array response = Array::Create();
  response.set(
    s_SassResponse_css,
    String::FromCStr(sass_context_get_output_string(ctx_w->ctx))
  );
  if (sass_option_get_source_map_file(opts) != 0) {
    response.set(
      s_SassResponse_map,
      String::FromCStr(sass_context_get_source_map_string(ctx_w->ctx))
    );
  }

  sass_free_context_wrapper(ctx_w);

  return response;
}

static String HHVM_METHOD(Sass, compile, const String& source) {
  VMRegAnchor _;

  struct Sass_Context_Wrapper* ctx_w
    = sass_make_data_context_wrapper(strdup(source.c_str()));

  Array response = compileScss(this_, ctx_w);

  return response[s_SassResponse_css].toString();
}

static Array HHVM_METHOD(Sass, compileWithMap, const String& source, const String& mapfile) {
  VMRegAnchor _;

  struct Sass_Context_Wrapper* ctx_w
    = sass_make_data_context_wrapper(strdup(source.c_str()));
  struct Sass_Options* opts = sass_context_get_options(ctx_w->ctx);

  sass_option_set_source_map_file(opts, mapfile.c_str());

  Array response = compileScss(this_, ctx_w);

  return response;
}

static String HHVM_METHOD(Sass, compileFileNative, const String& file) {
  VMRegAnchor _;

  struct Sass_Context_Wrapper* ctx_w
    = sass_make_file_context_wrapper(file.c_str());

  Array response = compileScss(this_, ctx_w);

  return response[s_SassResponse_css].toString();
}

static Array HHVM_METHOD(Sass, compileFileWithMapNative, const String& file, const String& mapfile) {
  VMRegAnchor _;

  struct Sass_Context_Wrapper* ctx_w
    = sass_make_file_context_wrapper(file.c_str());
  struct Sass_Options* opts = sass_context_get_options(ctx_w->ctx);

  sass_option_set_source_map_file(opts, mapfile.c_str());

  Array response = compileScss(this_, ctx_w);

  return response;
}

static String HHVM_STATIC_METHOD(Sass, sass2scss, const String& sass, int64_t options) {
  char* scss = sass2scss(sass.c_str(), options);
  String s(scss);
  free(scss);
  return s;
}

static String HHVM_STATIC_METHOD(Sass, getLibraryVersion) {
  return String::FromCStr(libsass_version());
}

static String HHVM_STATIC_METHOD(Sass, getLanguageVersion) {
  return String::FromCStr(libsass_language_version());
}

static String HHVM_STATIC_METHOD(Sass, getSass2ScssVersion) {
  return String::FromCStr(sass2scss_version());
}

static String HHVM_STATIC_METHOD(SassTypesString, quoteNative, const String& str) {
  char* quoted = sass_string_quote(str.c_str(), '*');
  String q(quoted);
  free(quoted);
  return q;
}

static String HHVM_STATIC_METHOD(SassTypesString, unquoteNative, const String& str) {
  char* unquoted = sass_string_unquote(str.c_str());
  String uq(unquoted);
  free(unquoted);
  return uq;
}

static class SassExtension : public Extension {
 public:
  SassExtension() : Extension("sass", "0.2-dev") {}
  virtual void moduleInit() {
    HHVM_MALIAS(Sass\\Sass, compile, Sass, compile);
    HHVM_MALIAS(Sass\\Sass, compileWithMap, Sass, compileWithMap);
    HHVM_MALIAS(Sass\\Sass, compileFileNative, Sass, compileFileNative);
    HHVM_MALIAS(Sass\\Sass, compileFileWithMapNative, Sass, compileFileWithMapNative);
    HHVM_STATIC_MALIAS(Sass\\Sass, sass2scss, Sass, sass2scss);
    HHVM_STATIC_MALIAS(Sass\\Sass, getLibraryVersion, Sass, getLibraryVersion);
    HHVM_STATIC_MALIAS(Sass\\Sass, getLanguageVersion, Sass, getLanguageVersion);
    HHVM_STATIC_MALIAS(Sass\\Sass, getSass2ScssVersion, Sass, getSass2ScssVersion);

    Native::registerClassConstant<KindOfInt64>(s_Sass.get(),
                                               s_STYLE_NESTED.get(),
                                               SASS_STYLE_NESTED);
    Native::registerClassConstant<KindOfInt64>(s_Sass.get(),
                                               s_STYLE_EXPANDED.get(),
                                               SASS_STYLE_EXPANDED);
    Native::registerClassConstant<KindOfInt64>(s_Sass.get(),
                                               s_STYLE_COMPACT.get(),
                                               SASS_STYLE_COMPACT);
    Native::registerClassConstant<KindOfInt64>(s_Sass.get(),
                                               s_STYLE_COMPRESSED.get(),
                                               SASS_STYLE_COMPRESSED);

    Native::registerClassConstant<KindOfInt64>(s_Sass.get(),
                                               s_SYNTAX_SCSS.get(),
                                               i_SYNTAX_SCSS);
    Native::registerClassConstant<KindOfInt64>(s_Sass.get(),
                                               s_SYNTAX_SASS.get(),
                                               i_SYNTAX_SASS);

    Native::registerClassConstant<KindOfInt64>(s_Sass.get(),
                                               s_SASS2SCSS_PRETTIFY_0.get(),
                                               SASS2SCSS_PRETTIFY_0);
    Native::registerClassConstant<KindOfInt64>(s_Sass.get(),
                                               s_SASS2SCSS_PRETTIFY_1.get(),
                                               SASS2SCSS_PRETTIFY_1);
    Native::registerClassConstant<KindOfInt64>(s_Sass.get(),
                                               s_SASS2SCSS_PRETTIFY_2.get(),
                                               SASS2SCSS_PRETTIFY_2);
    Native::registerClassConstant<KindOfInt64>(s_Sass.get(),
                                               s_SASS2SCSS_PRETTIFY_3.get(),
                                               SASS2SCSS_PRETTIFY_3);

    Native::registerClassConstant<KindOfInt64>(s_Sass.get(),
                                               s_SASS2SCSS_KEEP_COMMENT.get(),
                                               SASS2SCSS_KEEP_COMMENT);
    Native::registerClassConstant<KindOfInt64>(s_Sass.get(),
                                               s_SASS2SCSS_STRIP_COMMENT.get(),
                                               SASS2SCSS_STRIP_COMMENT);
    Native::registerClassConstant<KindOfInt64>(s_Sass.get(),
                                               s_SASS2SCSS_CONVERT_COMMENT.get(),
                                               SASS2SCSS_CONVERT_COMMENT);

    HHVM_STATIC_MALIAS(Sass\\Types\\SassString, quoteNative, SassTypesString, quoteNative);
    HHVM_STATIC_MALIAS(Sass\\Types\\SassString, unquoteNative, SassTypesString, unquoteNative);

    loadSystemlib();
  }
} s_sass_extension;

HHVM_GET_MODULE(sass)

} // namespace HPHP
