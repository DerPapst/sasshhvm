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

if [ "$HPHP_HOME" != "" -a -x "${HPHP_HOME}/hphp/test/run" ]; then
    TESTRUNNER="${HPHP_HOME}/hphp/test/run"
    $TESTRUNNER -r tests
else
    $HHVM \
      -vDynamicExtensions.0=${DIRNAME}/sass.so \
      ${DIRNAME}/test.php
fi


