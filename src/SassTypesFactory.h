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

#ifndef __EXT_SASS_SASSTYPESFACTORY_H__
#define __EXT_SASS_SASSTYPESFACTORY_H__

namespace HPHP {

extern const StaticString s_MongoDriverCommand_className;

extern const StaticString s_Type_SassNull_className;
extern const StaticString s_Type_SassNumber_className;
extern const StaticString s_Type_SassString_className;
extern const StaticString s_Type_SassBoolean_className;
extern const StaticString s_Type_SassColor_className;
extern const StaticString s_Type_SassList_className;
extern const StaticString s_Type_SassMapPair_className;
extern const StaticString s_Type_SassMap_className;
extern const StaticString s_Type_SassWarning_className;
extern const StaticString s_Type_SassError_className;


extern const StaticString s_Type_SassValueMethod_setValue;
extern const StaticString s_Type_SassValueMethod_setMessage;
extern const StaticString s_Type_SassValueMethod_addAll;
extern const StaticString s_Type_SassValueMethod_setSeparator;
extern const StaticString s_Type_SassValueMethod_set;

class SassTypesFactory {
  public:
    static Variant to_php(Sass_Value*);
    static union Sass_Value* to_sass(const Variant&);

  private:
    static Object php_create_and_construct(StaticString classname);
    static Variant to_php_null();
    static Variant to_php_number(Sass_Value*);
    static Variant to_php_string(Sass_Value*);
    static Variant to_php_boolean(Sass_Value*);
    static Variant to_php_color(Sass_Value*);
    static Variant to_php_list(Sass_Value*);
    static Variant to_php_map(Sass_Value*);
    static Variant to_php_warning(Sass_Value*);
    static Variant to_php_error(Sass_Value*);

    static union Sass_Value* to_sass_null();
    static union Sass_Value* to_sass_number(const Object&);
    static union Sass_Value* to_sass_string(const Object&);
    static union Sass_Value* to_sass_boolean(const Object&);
    static union Sass_Value* to_sass_color(const Object&);
    static union Sass_Value* to_sass_list(const Object&);
    static union Sass_Value* to_sass_map(const Object&);
    static union Sass_Value* to_sass_warning(const Object&);
    static union Sass_Value* to_sass_error(const Object&);
};

}

#endif
