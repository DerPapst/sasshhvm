/*
   +----------------------------------------------------------------------+
   | Integration of libsass for HHVM                                      |
   +----------------------------------------------------------------------+
   | Copyright (c) 2015 Alexander Papst                                   |
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
#include "hphp/runtime/base/execution-context.h"

#include "lib/libsass/include/sass.h"

namespace HPHP {

const StaticString s_Sass("Sass");

const StaticString s_STYLE_NESTED("STYLE_NESTED");
const StaticString s_STYLE_EXPANDED("STYLE_EXPANDED");
const StaticString s_STYLE_COMPACT("STYLE_COMPACT");
const StaticString s_STYLE_COMPRESSED("STYLE_COMPRESSED");

const StaticString s_SYNTAX_SCSS("SYNTAX_SCSS");
const StaticString s_SYNTAX_SASS("SYNTAX_SASS");
const int64_t i_SYNTAX_SCSS = 1;
const int64_t i_SYNTAX_SASS = 2;

const StaticString s_SassException("SassException");
#ifdef __WIN__
const StaticString s_Glue(";");
#else
const StaticString s_Glue(",");
#endif

const StaticString s_SassResponse_css("css");
const StaticString s_SassResponse_map("map");


static Class* c_SassException = nullptr;
static Object throwSassExceptionObject(const Variant& message, int64_t code) {
  if (!c_SassException) {
    c_SassException = Unit::lookupClass(s_SassException.get());
    assert(c_SassException);
  }

  Object obj{c_SassException};
  TypedValue ret;
  g_context->invokeFunc(&ret,
                        c_SassException->getCtor(),
                        make_packed_array(message, code),
                        obj.get());
  tvRefcountedDecRef(&ret);
  throw obj;
}

static void set_options(ObjectData* obj, struct Sass_Context *ctx) {
  struct Sass_Options* opts = sass_context_get_options(ctx);

  // All options have been validated in ext_sass.php
  sass_option_set_precision(opts, obj->o_get("precision", true, s_Sass).toInt64Val());

  sass_option_set_output_style(opts, (Sass_Output_Style)obj->o_get("style", true, s_Sass).toInt64Val());

  Array includePaths = obj->o_get("includePaths", true, s_Sass).toCArrRef();
  if (!includePaths.empty()) {
    sass_option_set_include_path(opts, StringUtil::Implode(includePaths, s_Glue).c_str());
  }

  bool includeSourceComments = obj->o_get("sourceComments", true, s_Sass).toBooleanVal();
  sass_option_set_source_comments(opts, includeSourceComments);
  if (includeSourceComments) {
    sass_option_set_omit_source_map_url(opts, false);
  }

  sass_option_set_is_indented_syntax_src(opts, obj->o_get("syntax", true, s_Sass).toInt64Val() == i_SYNTAX_SASS);

  if (!obj->o_get("linefeed", true, s_Sass).isNull()) {
    sass_option_set_linefeed(opts, obj->o_get("linefeed", true, s_Sass).toString().c_str());
  }
  if (!obj->o_get("indent", true, s_Sass).isNull()) {
    sass_option_set_indent(opts, obj->o_get("indent", true, s_Sass).toString().c_str());
  }

  sass_option_set_source_map_embed(opts, obj->o_get("embedMap", true, s_Sass).toBooleanVal());

  if (!obj->o_get("sourceRoot", true, s_Sass).isNull()) {
    sass_option_set_source_map_root(opts, obj->o_get("sourceRoot", true, s_Sass).toString().c_str());
  }
}

static String HHVM_METHOD(Sass, compile, const String& source) {
  // Create a new sass_context
  struct Sass_Data_Context* data_context = sass_make_data_context(strdup(source.c_str()));
  struct Sass_Context* ctx = sass_data_context_get_context(data_context);

  set_options(this_, ctx);

  int64_t status = sass_compile_data_context(data_context);

  // Check the context for any errors...
  if (status != 0) {
    String exMsg = String::FromCStr(sass_context_get_error_message(ctx));
    sass_delete_data_context(data_context);
    
    throwSassExceptionObject(exMsg, status);
  }
  
  String rt = String::FromCStr(sass_context_get_output_string(ctx));
  sass_delete_data_context(data_context);
  
  return rt;
}

static String HHVM_METHOD(Sass, compileFileNative, const String& file) {
  // Create a new sass_context
  struct Sass_File_Context* file_ctx = sass_make_file_context(file.c_str());
  struct Sass_Context* ctx = sass_file_context_get_context(file_ctx);

  set_options(this_, ctx);

  int64_t status = sass_compile_file_context(file_ctx);

  // Check the context for any errors...
  if (status != 0) {
    String exMsg = String::FromCStr(sass_context_get_error_message(ctx));
    sass_delete_file_context(file_ctx);
    
    throwSassExceptionObject(exMsg, status);
  }

  String rt = String::FromCStr(sass_context_get_output_string(ctx));
  sass_delete_file_context(file_ctx);

  return rt;
}

static Array HHVM_METHOD(Sass, compileFileWithMapNative, const String& file, const String& mapfile) {
  // Create a new sass_context
  struct Sass_File_Context* file_ctx = sass_make_file_context(file.c_str());
  struct Sass_Context* ctx = sass_file_context_get_context(file_ctx);
  struct Sass_Options* opts = sass_context_get_options(ctx);

  set_options(this_, ctx);

  sass_option_set_omit_source_map_url(opts, false);
  sass_option_set_source_map_contents(opts, true);
  sass_option_set_source_map_file(opts, mapfile.c_str());

  int64_t status = sass_compile_file_context(file_ctx);

  // Check the context for any errors...
  if (status != 0) {
    String exMsg = String::FromCStr(sass_context_get_error_message(ctx));
    sass_delete_file_context(file_ctx);

    throwSassExceptionObject(exMsg, status);
  }

  Array response = Array::Create();
  response.set(s_SassResponse_css, String::FromCStr(sass_context_get_output_string(ctx)));
  response.set(s_SassResponse_map, String::FromCStr(sass_context_get_source_map_string(ctx)));

  sass_delete_file_context(file_ctx);

  return response;
}

static String HHVM_STATIC_METHOD(Sass, getLibraryVersion) {
  return libsass_version();
}

static class SassExtension : public Extension {
 public:
  SassExtension() : Extension("sass", "0.1-dev") {}
  virtual void moduleInit() {
    HHVM_ME(Sass, compile);
    HHVM_ME(Sass, compileFileNative);
    HHVM_ME(Sass, compileFileWithMapNative);
    HHVM_STATIC_ME(Sass, getLibraryVersion);
    
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
    loadSystemlib();
  }
} s_sass_extension;

HHVM_GET_MODULE(sass)

} // namespace HPHP
