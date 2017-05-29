#!/bin/sh
cd tests/
cp ../ext_sass.hhi ext_sass.hhi
hh_client
rm ext_sass.hhi
cd ..

