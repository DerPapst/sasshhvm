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
#include "hphp/runtime/base/collections.h"
#include "hphp/runtime/base/array-init.h"

#include "../lib/libsass/include/sass.h"

#include "CallbackBridge.h"
#include "CustomFunctionBridge.h"
#include "SassTypesFactory.h"

namespace HPHP {

Array CustomFunctionBridge::pre_process_args(std::vector<void*> in) const {
  Array argv;
  for (void* value : in) {
    argv.append(SassTypesFactory::to_php(static_cast<Sass_Value*>(value)));
  }
  // For typing reasons pass an immutable vector containing the custom
  // functions parameters to the executing callback.
  return make_packed_array(Object::attach(
    argv.empty()
      ? collections::alloc(CollectionType::ImmVector)
      : collections::alloc(CollectionType::ImmVector, argv.detach())
  ));
}

union Sass_Value* CustomFunctionBridge::post_process_return_value(const Variant& val) const {
  return SassTypesFactory::to_sass(val);
}

union Sass_Value* CustomFunctionBridge::makeError(const char* message) const {
  return sass_make_error(message);
}

}
