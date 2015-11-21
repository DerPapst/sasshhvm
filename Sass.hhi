<?hh // decl

/**
 * HHVM bindings to libsass - Fast, native Sass compiling in HHVM!
 *
 * YOU SHOULD NEVER INCLUDE THIS FILE ANYWHERE!!!
 * This files purpose is solely for the type checker and for
 * documentation purposes.
 *
 * https://github.com/derpapst/sasshhvm
 * Based on https://github.com/sensational/sassphp/
 * Copyright (c)2015 Alexander Papst <http://derpapst.org>
 */
class Sass
{
    const int STYLE_NESTED = 0;
    const int STYLE_EXPANDED = 0;
    const int STYLE_COMPACT = 0;
    const int STYLE_COMPRESSED = 0;

    const int SYNTAX_SCSS = 0;
    const int SYNTAX_SASS = 0;

    /**
     * Complile a valid scss string to css.
     * @param string $source - String containing valid scss source code.
     * @throws SassException - If the source is invalid.
     * @return string - Compiled css code
     */
    final public function compile(string $source): string;

    /**
     * Compile a file containing valid scss to css.
     * Only local files without the use of a stream or wrapper are supported.
     * @param string $fileName
     *    String containing the path to a scss source code file.
     * @throws SassException
     *    If the file can not be read or source is invalid.
     * @return string - Compiled css code
     */
    final public function compileFile(string $fileName): string;

    /**
     * Compile a file containing valid scss to css including the corresponding
     * map file contents.
     * Only local files without the use of a stream or wrapper are supported.
     * The content for a matching map file will be returned as well.
     * @param string $fileName
     *    String containing the path to a scss source code file.
     * @throws SassException
     *    If the file can not be read or source is invalid.
     * @return SassResonse
     *    A shape containing the indexes 'css' which contains the compiled
     *    css and 'map' which contains the map contents.
     */
    final public function compileFileWithMap(string $fileName, ?string $mapFileName = null): SassResonse;

    /**
     * Get the currently used formatting style. Default is Sass::STYLE_NESTED.
     * @return int
     */
    public function getStyle(): int;

    /**
     * Set the formatting style.
     * Available styles are:
     *  * Sass::STYLE_NESTED
     *  * Sass::STYLE_EXPANDED
     *  * Sass::STYLE_COMPACT
     *  * Sass::STYLE_COMPRESSED
     * @param int $style
     * @throws \InvalidArgumentException - If the style is not supported.
     * @return self
     */
    final public function setStyle(int $style): this;

    /**
     * Get the currently used syntax type. Default is Sass::SYNTAX_SCSS.
     * @return int
     */
    public function getSyntax();

    /**
     * Set the syntax type for the input files/strings.
     * Available syntaxes are:
     *   * Sass::SYNTAX_SCSS
     *   * Sass::SYNTAX_SASS
     * @param int $syntax
     * @throws \InvalidArgumentException - If the syntax is not supported.
     * @return self
     */
    final public function setSyntax(int $syntax): this;

    /**
     * Gets the currently used include paths where the compiler will search for
     * included files.
     * @return array
     */
    public function getIncludePaths(): array<string>;

    /**
     * Add a path for searching for included files.
     * Only local directories without the use of a stream or wrapper
     * are supported.
     * @param string $includePath - The path to look for further sass files.
     * @throws RuntimeException - If the path does not exist or is not readable.
     * @return self
     */
    final public function addIncludePath(string $includePath): this;

    /**
     * Sets the include path list. Any previously set paths will be
     * overwritten.
     * Only local directories without the use of a stream or wrapper
     * are supported.
     * @param array<string> $includePaths
     *     The paths to look for further sass files.
     * @throws SassException - If one path does not exist or is not readable.
     * @return self
     */
    final public function setIncludePaths(array<string> $includePaths): this;

    /**
     * Get the currently used precision for decimal numbers.
     * @return int
     */
    public function getPrecision(): int;

    /**
     * Set the precision that will be used for decimal numbers.
     * @param int $precision
     * @return self
     */
    final public function setPrecision(int $precision): this;

    /**
     * Returns whether the compiled css files contain comments
     * indicating the corresponding source line.
     * @return bool
     */
    public function getIncludesSourceComments(): bool;

    /**
     * Pass true to enable emitting comments in the generated CSS indicating
     * the corresponding source line.
     * @param bool $sourceComments
     * @return self
     */
    final public function setIncludesSourceComments(bool $sourceComments): this;

    /**
     * Alias of self::getIncludesSourceComments()
     * @return bool
     */
    public function includesSourceComments(): bool;

    /**
     * Alias of self::includeSourceComments()
     * @param bool $sourceComments
     * @return self
     */
    final public function includeSourceComments(bool $sourceComments): this;

    /**
     * Get the string that will be used for line feeds in the compiled CSS.
     * If null is returned libsass' default will be used.
     * @return ?string
     */
    public function getLinefeed(): ?string;

    /**
     * Set the string to be used to for line feeds in the compiled CSS.
     * Pass null if you want to use the default from libsass.
     * @param ?string $linefeed
     * @return self
     */
    final public function setLinefeed(?string $linefeed): this;

    /**
     * Get the string that will be used for indentation in the compiled CSS.
     * If null is returned libsass' default will be used.
     * @return ?string
     */
    public function getIndent(): ?string;

    /**
     * Set the string to be used to for indentation in the compiled CSS.
     * Pass null if you want to use the default from libsass.
     * @param ?string $indent
     * @return self
     */
    final public function setIndent(?string $indent): this;

    /**
     * Returns true if the source mapping url is embedded in the compiled CSS
     * as data uri.
     * @return bool
     */
    public function getEmbedMap(): bool;

    /**
     * Control if the source mapping url is embedded in the compiled CSS
     * as data uri.
     * @param bool $embedMap
     * @return self
     */
    final public function setEmbedMap(bool $embedMap): this;

    /**
     * Alias of self::getEmbedMap()
     * @return bool
     */
    public function isMapEmbedded(): bool;

    /**
     * Alias of self::setEmbedMap()
     * @param bool $embedMap
     * @return self
     */
    final public function embedMap(bool $embedMap): this;

    /**
     * Get the pass-through for the sourceRoot property.
     * If null is returned the sourceRoot property is not populated.
     * @return ?string
     */
    public function getSourceRoot(): ?string;

    /**
     * Set the pass-through for the sourceRoot property.
     * Pass null if you do not want to populate the sourceRoot property.
     * @return self
     */
    final public function setSourceRoot(?string $sourceRoot): this;

    /**
     * Get the library version of libsass.
     * @return string
     */
    final public static function getLibraryVersion(): string;
}

/**
 * Exception for Sass.
 */
class SassException extends Exception
{
}

/**
 * Return type for Sass::compile*WithMap();
 */
type SassResonse = shape('css' => string, 'map' => string);
