# sasshhvm

The `sass` extension for [HHVM](https://github.com/facebook/hhvm) gives you an object-oriented system of parsing [Sass](http://sass-lang.com/) from within your PHP applications. Under the hood it uses [libsass](https://github.com/sass/libsass), a C library to parse and compile sass/scss files that does not require ruby.
It is based on the [sass extension for php](https://github.com/sensational/sassphp).

## What's Sass?

Sass is a CSS pre-processor language to add on exciting, new, awesome features to CSS. Sass was the first language of its kind and by far the most mature and up to date codebase.

Sass was originally created by Hampton Catlin ([@hcatlin](http://twitter.com/hcatlin)). The extension and continuing evolution of the language has all been the result of years of work by Natalie Weizenbaum ([@nex4](http://twitter.com/nex3)) and Chris Eppstein ([@chriseppstein](http://twitter.com/chriseppstein)).

For more information about Sass itself, please visit [http://sass-lang.com](http://sass-lang.com)

### Building & Installation

Requires HHVM 3.6 or later and either the hhvm source tree (use the variable $HPHP_HOME to point to your hhvm source tree) or the [hhvm-dev package](https://github.com/facebook/hhvm/wiki/Prebuilt-Packages-for-HHVM).

Update the submodule with 
~~~
git submodule update --init --recursive
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

This extension has a very simple API. You can find the entire API [here](Sass.hhi).

```php
$sass = new Sass();
$css = $sass->compile($source);
```

You can compile a file with `compileFile()`:

```php
$sass = new Sass();
$css = $sass->compileFile($file);
```

You can set the include path for the library to use:

```php
$sass = new Sass();
$sass->addIncludePath('/tmp');
$css = $sass->compile($source);
```

The style of the compiled css can be changed:

```php
$sass->setStyle(Sass::STYLE_EXPANDED)->setLinefeed("\r\n")->setIndent("\t");
```

Sass syntax is supported as well.

```php
$sass->setSyntax(Sass::SYNTAX_SASS);
```

### Maps

There are 2 ways to generate maps.

You can embed them in the compiled css as data uri ...

```php
$css = $sass->embedMap(true)->compile($source);
```

or the map can be generated as a separate file as well.

```php
$cssAndMap = $sass->compileFileWithMap($file);
// Or with an alternative map file name ...
// The file won't be written to the filesystem.
$cssAndMap = $sass->compileFileWithMap($file, $mapfile);
```

`$cssAndMap` is a [shape](http://docs.hhvm.com/manual/en/hack.shapes.php) (an array for the runtime) containing the elements "css" and "map", where "css" contains the compiled css (duh) and "map" contains the source map output.

The sourceRoot property of the map files can be set with:

```php
$css = $sass->setSourceRoot('/herp/derp/');
```

### Errors

If there's a problem with compiling your SCSS or Sass, the extension will throw a `SassException`:

```php
$sass = new Sass();
try {
    $css = $sass->compile('asdf');
} catch (SassException $e) {
    // $e->getMessage() --> 'Invalid CSS after "asdf": expected "{", was ""'
    $css = null;
}
```

`SassException` has some additional methods to get more information about the compilation error like the sources file name, line and column number and a formatted error message.

```php
$e->getSourceFile();
$e->getSourceLine();
$e->getSourceColumn();
$e->getFormattedMessage();
```
