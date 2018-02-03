#!/bin/sh

if [ "$HPHP_HOME" != "" ] && [ -x "${HPHP_HOME}/hphp/hack/bin/hh_server" ]; then
    HH_SERVER="${HPHP_HOME}/hphp/hack/bin/hh_server"
else
    HH_SERVER=hh_server
fi

cd tests/
cp ../ext_sass.hhi ext_sass.hhi
$HH_SERVER --check .
rm ext_sass.hhi
cd ..
