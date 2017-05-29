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

#ifndef __EXT_SASS_CUSTOMIMPORTERBRIDGE_H__
#define __EXT_SASS_CUSTOMIMPORTERBRIDGE_H__

#include "../lib/libsass/include/sass.h"

#include "CallbackBridge.h"

namespace HPHP {

class CustomImporterBridge : public CallbackBridge<Sass_Import_List> {
  public:
    CustomImporterBridge(const Variant& callback) : CallbackBridge<Sass_Import_List>(callback) {}

  private:
    Array pre_process_args(std::vector<void*>) const;
    Sass_Import_List post_process_return_value(const Variant&) const;
    Sass_Import_List makeError(const char*) const;

    ArrayIter get_arrayiter_helper(const Variant&, size_t&) const;
    Sass_Import* get_importer_entry(const Variant&) const;
};

}

#endif
