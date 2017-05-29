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

#ifndef __EXT_SASS_CUSTOMFUNCTIONBRIDGE_H__
#define __EXT_SASS_CUSTOMFUNCTIONBRIDGE_H__

#include "../lib/libsass/include/sass.h"

#include "CallbackBridge.h"

namespace HPHP {

class CustomFunctionBridge : public CallbackBridge<Sass_Value*> {
  public:
    CustomFunctionBridge(const Variant& callback) : CallbackBridge<Sass_Value*>(callback) {}

  private:
    Array pre_process_args(std::vector<void*>) const;
    union Sass_Value* post_process_return_value(const Variant&) const;
    union Sass_Value* makeError(const char*) const;
};

}

#endif
