# sasshhvm

The `sass` extension for [HHVM](https://github.com/facebook/hhvm) gives you an object-oriented system of parsing [Sass](http://sass-lang.com/) from within your PHP applications. Under the hood it uses [libsass](https://github.com/sass/libsass), a C library to parse and compile sass/scss files that does not require ruby.
It is based on the [sass extension for php](https://github.com/sensational/sassphp).

## What's Sass?

Sass is a CSS pre-processor language to add on exciting, new, awesome features to CSS. Sass was the first language of its kind and by far the most mature and up to date codebase.

Sass was originally created by Hampton Catlin ([@hcatlin](http://twitter.com/hcatlin)). The extension and continuing evolution of the language has all been the result of years of work by Natalie Weizenbaum ([@nex4](http://twitter.com/nex3)) and Chris Eppstein ([@chriseppstein](http://twitter.com/chriseppstein)).

For more information about Sass itself, please visit [http://sass-lang.com](http://sass-lang.com)

### Building & Installation

Requires HHVM 3.2 or later and either the hhvm source tree (use the variable $HPHP_HOME to point to your hhvm source tree) or the [hhvm-dev package](https://github.com/facebook/hhvm/wiki/Prebuilt-Packages-for-HHVM).

Update the submodule with 
~~~
git submodule update --init --recursive`
~~~
and then run

~~~
./build.sh
~~~


To enable the extension, you need to have the following section in your PHP ini file:

~~~
hhvm.dynamic_extension_path = /path/to/hhvm/extensions
hhvm.dynamic_extensions[sass] = sass.so
~~~

Where `/path/to/hhvm/extensions` is a folder containing all HHVM extensions,
and `sass.so` is in it. This will cause the extension to be loaded when the
virtual machine starts up.

### Testing

To run the test suite:

~~~
$ cd /path/to/extension
$ ./test.sh
~~~

If you have the complete hhvm source tree you can run the tests with the test runner.

~~~
HPHP_HOME=/path/to/hhvm/source ./test.sh
~~~


## Usage

This extension has a very simple API. You can find the entire API [here](ext_sass.php).

```php
$sass = new Sass();
$css = $sass->compile($source);
```

You can compile a file with `compileFile()`:

```php
$sass = new Sass();
$css = $sass->compileFile($source);
```

You can set the include path for the library to use:

```php
$sass = new Sass();
$sass->addIncludePath('/tmp');
$css = $sass->compile($source);
```

If there's a problem, the extension will throw a `SassException`:

```php
$sass = new Sass();
try {
    $css = $sass->compile('asdf');
} catch (SassException $e) {
    // $e->getMessage() - ERROR -- , line 1: invalid top-level expression
    $css = false;
}
```
