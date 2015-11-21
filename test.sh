#!/bin/sh

DIRNAME=`dirname $0`
REALPATH=`which realpath`

if [ ! -z "${REALPATH}" ]; then
    DIRNAME=`realpath ${DIRNAME}`
fi

if [ "$HPHP_HOME" != "" ]; then
    HHVM="${HPHP_HOME}/hphp/hhvm/hhvm"
else
    HHVM=hhvm
fi

if [ "$#" -eq 1 ]; then
    if [ -e "$1" ]; then
        $HHVM -c ${DIRNAME}/tests/config.ini -vHack.Lang.AutoTypecheck=0 $1
    else
        echo $1 does not exist.
    fi
else
    TESTRUNNER="${HPHP_HOME}/hphp/test/run"
    if [ "$HPHP_HOME" != "" -a -x $TESTRUNNER ]; then
        $TESTRUNNER -r tests
    else
        $HHVM \
          -vDynamicExtensions.0=${DIRNAME}/sass.so \
          ${DIRNAME}/test.php
    fi
fi
