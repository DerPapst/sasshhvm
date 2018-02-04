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
#include "hphp/runtime/base/backtrace.h"
#include "hphp/runtime/base/builtin-functions.h"
#include "hphp/runtime/base/execution-context.h"
#include "hphp/runtime/ext/std/ext_std_errorfunc.h"
#include "hphp/runtime/ext/std/ext_std_variable.h"
#include "hphp/runtime/vm/native-data.h"
#include "hphp/runtime/ext/json/ext_json.h"

#include "ExceptionManager.h"

namespace HPHP {

const StaticString s_SassException("Sass\\SassException");
const StaticString s_ErrorException("ErrorException");

const StaticString s_Property_file("file");
const StaticString s_Property_line("line");
const StaticString s_Property_column("column");
const StaticString s_Property_message("message");
const StaticString s_Property_formatted("formatted");
const StaticString s_Property_trace("trace");
const StaticString s_Property_function("function");

ExceptionManager* ExceptionManager::_instance = 0;

Variant ExceptionManager::getLast() {
  return exception;
}

void ExceptionManager::setLast(Variant e) {
  exception = e;
  exceptionSet = true;
}

void ExceptionManager::resetLast() {
  if (exceptionSet) {
    exception = uninit_variant;
  }
  exceptionSet = false;
}

bool ExceptionManager::hasLast() {
  return exceptionSet;
}

Object ExceptionManager::createSassException(const String& message, const String& jsonMessage, int64_t code) {
  Array exParams;

  // decode the json message and prefer that if it is complete.
  Variant jres = Variant::attach(HHVM_FN(json_decode)(jsonMessage, true));
  if (jres.isArray()) {
    Array jmesg = jres.toArray();
    exParams.set(0,
                 jmesg.exists(s_Property_message) && !jmesg[s_Property_message].isNull()
                     ? jmesg[s_Property_message].toString()
                     : message);
    exParams.set(1, code);
    if (jmesg.exists(s_Property_file) && !jmesg[s_Property_file].isNull()) {
      exParams.set(2, jmesg[s_Property_file].toString());
    } else {
      exParams.set(2, null_string);
    }
    if (jmesg.exists(s_Property_line) && !jmesg[s_Property_line].isNull()) {
      exParams.set(3, Variant(jmesg[s_Property_line].toInt64()));
    } else {
      exParams.set(3, uninit_variant);
    }
    if (jmesg.exists(s_Property_column) && !jmesg[s_Property_column].isNull()) {
      exParams.set(4, Variant(jmesg[s_Property_column].toInt64()));
    } else {
      exParams.set(4, uninit_variant);
    }
    if (jmesg.exists(s_Property_formatted) && !jmesg[s_Property_formatted].isNull()) {
      exParams.set(5, jmesg[s_Property_formatted].toString());
    } else {
      exParams.set(5, null_string);
    }
  } else {
    exParams = make_packed_array(message, code, null_string, uninit_variant, uninit_variant, null_string);
  }

  if (instance()->hasLast()) {
    exParams.append(instance()->getLast());
    instance()->resetLast();
  }

  return create_object(s_SassException, exParams);
}

Object ExceptionManager::convertFatalToErrorException(FatalErrorException throwable) {
  auto fileAndLine = throwable.getFileAndLine();
  auto trace = throwable.getBacktrace();
  auto e = create_object(
    s_ErrorException,
    make_packed_array(
      String(throwable.getMessage()),
      0,
      (int)ErrorMode::ERROR,
      fileAndLine.first,
      fileAndLine.second
    )
  );
  if (trace.exists(0) && trace.exists(1)
    && !trace[0].toArray().exists(s_Property_function)
    && trace[1].toArray().exists(s_Property_function)
  ) {
    auto frame = trace.dequeue().toArray();
    if (!trace[0].toArray().exists(s_Property_file) && frame.exists(s_Property_file)) {
      trace.set(0, frame.merge(trace[0].toArray()));
    }
  }
  auto const traceIdx = SystemLib::s_ExceptionClass->lookupDeclProp(s_Property_trace.get());
  if (traceIdx != kInvalidSlot) {
    auto const trace_lval = e->propLvalAtOffset(traceIdx);
    cellMove(make_tv<KindOfArray>(trace.detach()), trace_lval);
  }
  return e;
}

}
