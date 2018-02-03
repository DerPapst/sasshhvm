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

#ifndef __EXT_SASS_CALLBACKBRIDGE_H__
#define __EXT_SASS_CALLBACKBRIDGE_H__

#include "ExceptionManager.h"
#include "hphp/runtime/ext/std/ext_std_variable.h"

namespace HPHP {

const StaticString s_Exception_message("message");

template <typename T>
class CallbackBridge {
  public:
    CallbackBridge(const Variant& callback);
    virtual ~CallbackBridge();

    // Executes the callback
    T operator()(std::vector<void*>);

  protected:
    Variant callback;
    Variant str;
    const char* unknown_message = "An unknown error occured";

    const char* getMessageFromThrowable(const Object&);

    virtual Array pre_process_args(std::vector<void*>) const =0;
    virtual T post_process_return_value(const Variant&) const =0;
    virtual T makeError(const char* message) const =0;
};

template <typename T>
CallbackBridge<T>::CallbackBridge(const Variant& callback) {
  this->callback = callback;
}

template <typename T>
CallbackBridge<T>::~CallbackBridge() {

}

template <typename T>
const char* CallbackBridge<T>::getMessageFromThrowable(const Object& o) {
  try {
    // Get the message property from the Throwable if we can.
    // Otherwise, use the class name.
    assert(
      o->instanceof(SystemLib::s_ExceptionClass)
      || o->instanceof(SystemLib::s_ErrorClass)
    );

    auto const rval = o->getProp(
      o->instanceof(SystemLib::s_ExceptionClass)
        ? SystemLib::s_ExceptionClass
        : SystemLib::s_ErrorClass,
      s_Exception_message.get()
    );
    if (rval.has_val() && rval.type() == KindOfString) {
      auto val = tvCastToString(rval.tv());
      return val.c_str();
    }
  } catch (...) {}

  try {
    return o.toString().c_str();
  } catch (...) {
    return this->unknown_message;
  }
}

template <typename T>
T CallbackBridge<T>::operator()(std::vector<void*> argv) {
  try {
    return this->post_process_return_value(
      vm_call_user_func(this->callback, pre_process_args(argv))
    );
  } catch (const FatalErrorException& e) {
    // Convert catchable EngineExceptions to ErrorExceptions
    // and pass them to SassExceptions $previous parameter.
    // Not an ideal solution but i have no better idea.
    ExceptionManager::instance()->setLast(
      ExceptionManager::convertFatalToErrorException(e)
    );
    return this->makeError(e.getMessage().c_str());

  } catch (const Exception& e) {
    // Not really happy with how these exceptions are handled, but this is it for now.
    // It will be caught and converted to a SassException.
    // Information about the origin of the error (eg. file, line, trace)
    // will be lost if they were available in the first place.
    return this->makeError(e.getMessage().c_str());

  } catch (const Object& o) {
    // User exceptions and catchable php7 engine exceptions
    // will be added as $previous to the SassException instance.
    ExceptionManager::instance()->setLast(o);
    return this->makeError(
      this->getMessageFromThrowable(o)
    );

  } catch (...) {
    return this->makeError(this->unknown_message);
  }
}

}

#endif
