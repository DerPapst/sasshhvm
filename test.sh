#!/bin/sh

DIRNAME=`dirname $0`
REALPATH=`which realpath`

if [ ! -z "${REALPATH}" ]; then
    DIRNAME=`realpath ${DIRNAME}`
fi

if [ "$HPHP_HOME" != "" ]; then
    HHVM="${HPHP_HOME}/hphp/hhvm/hhvm"
    TESTRUNNER="${HPHP_HOME}/hphp/test/run"
else
    HHVM=hhvm
    TESTRUNNER="${DIRNAME}/run-test"
fi

TESTFILE="tests"
USETESTRUNNER=true

for i in "$@"
do
case $i in
    --no-runner|-nr)
    USETESTRUNNER=false;
    shift
    ;;
    *)
    TESTFILE=$i
    shift
esac
done

if [ $USETESTRUNNER = true -a -x $TESTRUNNER ]; then
    if [ -e $TESTFILE ]; then
        ${TESTRUNNER} -r ${TESTFILE}
    else
        echo "${TESTFILE} does not exist."
    fi
else
    if [ $USETESTRUNNER = true ]; then
        echo "Make sure ${TESTRUNNER} is executable or provide the path to a specific php file in the test directory as argument."
        exit
    fi

    if [ -f $TESTFILE -a -e $TESTFILE ]; then
        ARGS="-c ${DIRNAME}/tests/config.ini"
        if [ -f "${DIRNAME}/${TESTFILE}.ini" ]; then
            ARGS="-c ${DIRNAME}/${TESTFILE}.ini"
        fi
        cp "${DIRNAME}/ext_sass.hhi" "${DIRNAME}/tests/ext_sass.hhi"
        $HHVM ${ARGS} -d hhvm.hack.lang.auto_typecheck=1 ${TESTFILE}
        rm "${DIRNAME}/tests/ext_sass.hhi"
    else
        echo "${TESTFILE} does not exist or is no regular file."
    fi
fi
