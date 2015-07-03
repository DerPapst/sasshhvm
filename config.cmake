# Code shamelessly copied from http://stackoverflow.com/questions/7876753/reusing-custom-makefile-for-static-library-with-cmake

# set the output destination
set(LIBSASS_LIBRARY ${CMAKE_CURRENT_SOURCE_DIR}/lib/libsass/lib/libsass.a)
# create a custom target called build_libsass that is part of ALL
# and will run each time you type make 
add_custom_target(build_libsass ALL 
                   COMMAND ${CMAKE_MAKE_PROGRAM}
                   WORKING_DIRECTORY ${CMAKE_CURRENT_SOURCE_DIR}/lib/libsass
                   COMMENT "Original libsass makefile target")

# now create an imported static target
add_library(libsass STATIC IMPORTED)
# Import target "libsass" for configuration ""
set_property(TARGET libsass APPEND PROPERTY IMPORTED_CONFIGURATIONS NOCONFIG)
set_target_properties(libsass PROPERTIES
  IMPORTED_LOCATION_NOCONFIG "${LIBSASS_LIBRARY}")

# now you can use libsass as if it were a regular cmake built target in your project
add_dependencies(libsass build_libsass)

HHVM_EXTENSION(sass ext_sass.cpp)
HHVM_SYSTEMLIB(sass ext_sass.php)

target_link_libraries(sass libsass)

# note, this will only work on linux/unix platforms, also it does building
# in the source tree which is also sort of bad style and keeps out of source 
# builds from working.