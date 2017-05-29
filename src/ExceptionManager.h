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

#ifndef __EXT_SASS_EXCEPTIONMANAGER_H__
#define __EXT_SASS_EXCEPTIONMANAGER_H__

namespace HPHP {

class ExceptionManager {
  public:
    static ExceptionManager* instance () {
      static CGuard g;
      if (!_instance) {
        _instance = new ExceptionManager ();
        _instance->resetLast();
      }
      return _instance;
    }

    Variant getLast();
    void setLast(Variant);
    void resetLast();
    bool hasLast();
    static Object createException(StaticString exceptionName, Array args);
    static Object createSassException(const String& message, const String& jsonMessage, int64_t code);
    static Object convertFatalToErrorException(FatalErrorException h);

  private:
    static ExceptionManager* _instance;
    ExceptionManager () { }
    ExceptionManager ( const ExceptionManager& );
    ~ExceptionManager () { }
    class CGuard {
      public:
        ~CGuard() {
          if (nullptr != ExceptionManager::_instance) {
            delete ExceptionManager::_instance;
            ExceptionManager::_instance = nullptr;
          }
        }
    };

    Variant exception;
    bool exceptionSet;
};

}

#endif
