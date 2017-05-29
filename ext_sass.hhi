<?hh // decl

namespace Sass {

/**
 * HHVM bindings to libsass - Fast, native Sass compiling in HHVM!
 *
 * For a more detailed descriptions of all methods see Sass.hhi
 *
 * https://github.com/derpapst/sasshhvm
 * Based on https://github.com/sensational/sassphp/
 *
 * Copyright (c) 2015 - 2017 Alexander Papst
 */
class Sass
{
    const int STYLE_NESTED = 0;
    const int STYLE_EXPANDED = 0;
    const int STYLE_COMPACT = 0;
    const int STYLE_COMPRESSED = 0;

    const int SYNTAX_SCSS = 0;
    const int SYNTAX_SASS = 0;

    const int SASS2SCSS_PRETTIFY_0 = 0;
    const int SASS2SCSS_PRETTIFY_1 = 0;
    const int SASS2SCSS_PRETTIFY_2 = 0;
    const int SASS2SCSS_PRETTIFY_3 = 0;

    const int SASS2SCSS_KEEP_COMMENT = 0;
    const int SASS2SCSS_STRIP_COMMENT = 0;
    const int SASS2SCSS_CONVERT_COMMENT = 0;

    /**
     * Complile a scss string to css.
     *
     * @param $source - String containing valid scss source code.
     *
     * @throws SassException - If the source contains errors.
     *
     * @return - The compiled css code.
     */
    final public function compile(string $source): string;

    /**
     * Complile a scss string to css including the corresponding map
     * file contents.
     *
     * @param $source - String containing valid scss source code.
     *
     * @throws SassException - If the source contains errors.
     *
     * @return - A shape containing the indexes 'css' which contains the
     *           compiled css and 'map' which contains the map contents.
     */
    final public function compileWithMap(string $source, string $mapFileName): array;

    /**
     * Compile a file containing scss to css.
     * Only local files without the use of a stream or wrapper are supported.
     *
     * @param $fileName - String containing the path to a scss source code
     *                    file.
     *
     * @throws SassException - If the file can not be read or source contains
     *                         errors.
     *
     * @return string - Compiled css code
     */
    final public function compileFile(string $fileName): string;

    /**
     * Compile a file containing scss to css including the corresponding map
     * file contents.
     * Only local files without the use of a stream or wrapper are supported.
     * The content for a matching map file will be returned as well.
     *
     * @param $fileName - String containing the path to a scss source code
     *                    file.
     *
     * @throws SassException - If the file can not be read or source contains
     *                         errors.
     *
     * @return - A shape containing the indexes 'css' which contains the
     *           compiled css and 'map' which contains the map contents.
     */
    final public function compileFileWithMap(
        string $fileName,
        ?string $mapFileName = null
    ): SassResonse;

    /**
     * Get the currently used formatting style. Default is Sass::STYLE_NESTED.
     *
     * @return int
     */
    public function getStyle(): int;

    /**
     * Set the formatting style.
     * Available styles are:
     *    - Sass::STYLE_NESTED (default)
     *    - Sass::STYLE_EXPANDED
     *    - Sass::STYLE_COMPACT
     *    - Sass::STYLE_COMPRESSED
     *
     * @param int $style
     *
     * @throws \InvalidArgumentException - If the style is not supported.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setStyle(int $style): this;

    /**
     * Get the currently used syntax type. Default is Sass::SYNTAX_SCSS.
     *
     * @return - The currently used syntax type.
     */
    public function getSyntax(): int;

    /**
     * Set the syntax type for the input files/strings.
     * Available syntaxes are:
     *   * Sass::SYNTAX_SCSS (default)
     *   * Sass::SYNTAX_SASS
     *
     * @param $syntax - The new syntax type.
     *
     * @throws \InvalidArgumentException - If the syntax is not supported.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setSyntax(int $syntax): this;

    /**
     * Gets the currently used include paths where the compiler will search
     * for files to include.
     *
     * @return - A vector containing all additional include paths.
     */
    public function getIncludePaths(): ImmVector<string>;

    /**
     * Add a path for searching for included files.
     * Only local directories without the use of a stream or wrapper
     * are supported.
     *
     * @param $includePath - The path to look for further scss files.
     *
     * @throws \RuntimeException - If the path does not exist or is not
     *                             readable.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function addIncludePath(string $includePath): this;

    /**
     * Sets the include path list. Any previously set paths will be
     * overwritten.
     * Only local directories without the use of a stream or wrapper
     * are supported.
     *
     * @param $includePaths - The paths to look for further scss files.
     *
     * @throws \RuntimeException - If one path does not exist or is not
     *                             readable.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setIncludePaths(Traversable<string> $includePaths): this;

    /**
     * Get the currently used precision for decimal numbers.
     *
     * @return - Number of digits of the fractional part.
     */
    public function getPrecision(): int;

    /**
     * Set the precision that will be used for decimal numbers.
     *
     * @param $precision - Number of digits for the fractional part.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setPrecision(int $precision): this;

    /**
     * Returns whether the compiled css files will contain comments indicating
     * the corresponding source line.
     *
     * @return - `true` when source line comments will be emitted; `false`
     *           otherwise.
     */
    public function getIncludesSourceComments(): bool;

    /**
     * Sets whether to enable emitting comments in the generated CSS indicating
     * the corresponding source line or not.
     *
     * @param $sourceComments - Pass `true` to enable emmiting source line
     *                          comments; `false` otherwise.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setIncludesSourceComments(bool $sourceComments): this;

    /**
     * Alias of self::getIncludesSourceComments()
     */
    public function includesSourceComments(): bool;

    /**
     * Alias of self::includeSourceComments()
     */
    final public function includeSourceComments(bool $sourceComments): this;

    /**
     * Get the string that will be used for line feeds in the compiled CSS.
     * If null is returned `libsass`' default will be used.
     *
     * @return - The currently used line feed string.
     */
    public function getLinefeed(): ?string;

    /**
     * Set the string to be used to for line feeds in the compiled CSS.
     * Pass null if you want to use the default from `libsass`.
     *
     * @param $linefeed - The new line feed string.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setLinefeed(?string $linefeed): this;

    /**
     * Get the string that will be used for indentation in the compiled CSS.
     * If null is returned `libsass`' default will be used.
     *
     * @return - The currently used indentation string.
     */
    public function getIndent(): ?string;

    /**
     * Set the string to be used to for indentation in the compiled CSS.
     * Pass null if you want to use the default from `libsass`.
     *
     * @param $indent - The new indentation string.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setIndent(?string $indent): this;

    /**
     * Gets the information whether the the source mapping is embedded in
     * the compiled CSS as data uri.
     *
     * @return - `true` if the source mapping is embedded in the compiled CSS
     *           as data uri; `false` otherwise
     */
    public function getEmbedMap(): bool;

    /**
     * Control if the source mapping is embedded in the compiled CSS as data
     * uri.
     *
     * @param $embedMap - Whether the source mapping will be embedded or not.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setEmbedMap(bool $embedMap): this;

    /**
     * Alias of self::getEmbedMap()
     */
    public function isMapEmbedded(): bool;

    /**
     * Alias of self::setEmbedMap()
     */
    final public function embedMap(bool $embedMap): this;

    /**
     * Get the pass-through for the sourceRoot property in source maps.
     * If null is returned the sourceRoot property is not populated.
     *
     * @return - The value for the sourceRoot property.
     */
    public function getSourceRoot(): ?string;

    /**
     * Set the pass-through for the sourceRoot property in source maps.
     * Pass null if you do not want to populate the sourceRoot property.
     *
     * @param - The new value for the sourceRoot property.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setSourceRoot(?string $sourceRoot): this;

    /**
     * Sass functions are used to define new custom functions callable by Sass
     * code. They are also used to overload debug or error statements. You can
     * also define a fallback function, which is called for every unknown
     * function found in the Sass code. Functions get passed zero or more
     * `SassValue`s in an immutable vector and they must also return a
     * `SassValue`. Return a `SassError` if you want to signal an error.
     *
     * Special signatures
     *     - `*` - Fallback implementation
     *     - `@warn` - Overload warn statements
     *     - `@error` - Overload error statements
     *     - `@debug` - Overload debug statements
     *
     * Note: The fallback implementation will be given the name of the called
     *       function as the first argument, before all the original function
     *       arguments. These features are pretty new and should be considered
     *       experimental.
     *
     * @param $signature  - The function signature (including required and
     *                      optional parameters).
     * @param $fnCallback - The custom function that will be called by
     *                      `libsass`.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function addFunction(
        string $signature,
        (function (ImmVector<Types\SassValue>): ?Types\SassValue) $fnCallback
    ): this;

    /**
     * Remove a custom function based on its signature.
     *
     * @param $signature - The identifier of the importer to remove.
     *                      If no importer exists with this identifier the
     *                      list of importers remains unchanged.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function removeFunction(string $signature): this;

    /**
     * List all available custom functions.
     *
     * @return - The available custom functions.
     */
    public function listFunctions(): ImmVector<string>;

    /**
     * By using custom importers, Sass stylesheets can be implemented in any
     * possible way, such as by being loaded via a remote server.
     * Imports must be relative to the parent import context and therefore
     * this information is passed to the importer callback. This is currently
     * done by passing the complete import string/path of the previous import
     * context.
     *
     * You actually have to return a list of imports, since some importers may
     * want to import multiple files from one import statement
     * (ie. a glob/star importer).
     *
     * Every import will then be included in `libsass`. You are allowed to only
     * return a file path without any loaded source. This way you can ie.
     * implement rewrite rules for import paths and leave the loading part for
     * `libsass`.
     *
     * You are also allowed to return `null` instead of a list of `SassImport`
     * instances, which will tell `libsass` to pass the import instruction to
     * another custom importer or to handle the import by itself (as if no
     * custom importer was in use).
     *
     * @param $identifier - A identifier for the importer.
     * @param $fnCallback - The custom importer.
     * @param $priority   - A priority for the importer (default: 0).
     *                      The higher, the earlier it will be called by
     *                      `libsass`.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function addImporter(
        string $identifier,
        (function (string, string): ?Traversable<?SassImport>) $fnCallback,
        int $priority = 0
    ): this;

    /**
     * Remove an importer based on its identifier.
     *
     * @param $identifier - The identifier of the importer to remove.
     *                      If no importer exists with this identifier the
     *                      list of importers remains unchanged.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function removeImporter(string $identifier): this;

    /**
     * List all available custom importers and their priority.
     * The keys are the identifier, the values their priority.
     *
     * @return - The available importers.
     */
    final public function listImporters(): ImmMap<string, int>;

    /**
     * Update the priority of an available importer.
     *
     * @param $identifier - The identifier of the importer of which
     *                      the priority is to be changed.
     * @param $priority   - The new priority.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setImporterPriority(
        string $identifier,
        int $priority
    ): this;

    /**
     * Add a header to each compile*() run that will be prepended to the sass
     * source. As an example headers can be used to define different variables
     * that influence the resulting css for different compile runs.
     *
     * @param $identifier - A identifier for the header.
     * @param $content    - The content (scss) of the header.
     * @param $priority   - A priority for the header (default: 0). The higher,
     *                      the earlier it will be processed by `libsass`.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function addHeader(
        string $identifier,
        string $content,
        int $priority = 0
    ): this;

    /**
     * Remove a header based on its identifier.
     *
     * @param $identifier - The identifier of the header to remove.
     *                      If no header exists with this identifier the list
     *                      of headers remains unchanged.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function removeHeader(string $identifier): this;

    /**
     * List all available headers and their priority.
     * The keys are the identifier, the values their priority.
     *
     * @return - The available headers.
     */
    final public function listHeaders(): ImmMap<string, int>;

    /**
     * Set the priority of an available header.
     *
     * @param $identifier - The identifier of the header of which
     *                      the priority is to be changed.
     * @param $priority   - The new priority.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setHeaderPriority(string $identifier, int $priority): this;

    /**
     * A helper to convert sass to scss.
     *
     * @param $sass - The sass source code that shall be converted.
     * @param $options - Options that influence the formatting of the resulting scss
     *                   and the handling of comments.
     *                   The options can be combined using the bitwise OR operator.
     *                   Available options for formatting:
     *                       - Sass::SASS2SCSS_PRETTIFY_0
     *                       - Sass::SASS2SCSS_PRETTIFY_1
     *                       - Sass::SASS2SCSS_PRETTIFY_2
     *                       - Sass::SASS2SCSS_PRETTIFY_3
     *                   Available options for comment handling:
     *                       - Sass::SASS2SCSS_KEEP_COMMENT
     *                       - Sass::SASS2SCSS_STRIP_COMMENT
     *                       - Sass::SASS2SCSS_CONVERT_COMMENT
     *
     * @return - The converted scss source
     */
    final public static function sass2scss(string $sass, int $options = 2): string;

    /**
     * Get the library version of `libsass`.
     *
     * @return - The library version of `libsass`.
     */
    final public static function getLibraryVersion(): string;

    /**
     * Get the version of the sass specification `libsass` implements.
     *
     * @return - The language version that `libsass` implements.
     */
    final public static function getLanguageVersion(): string;

    /**
     * Get the version of the `sass2scss` utility `libsass` ships with.
     *
     * @return - The version of `sass2scss`.
     */
    final public static function getSass2ScssVersion(): string;
}

/**
 * Return type for Sass::compile*WithMap();
 */
type SassResonse = shape('css' => string, 'map' => string);

/**
 * Exception for Sass that includes additional information
 * about the location that caused the compilation error.
 */
class SassException extends \Exception
{

    /**
     * Create a new instance of SassException.
     *
     * @param $message    - The short version of the error message
     * @param $code       - The error code
     * @param $sourceFile - The path to the scss file that caused the error.
     * @param $sourceLine - The line in the scss source that caused the error.
     * @param $sourceColumn - The column in the scss source that caused the error.
     * @param $formattedMessage - The complete error message including a
     *                            backtrace from `libsass` showing the exact
     *                            position that caused the error.
     * @param $previous   - A previously thrown exception that caused the error.
     */
    public function __construct(
        ?string $message = null,
        int $code = 0,
        ?string $sourceFile = null,
        ?int $sourceLine = null,
        ?int $sourceColumn = null,
        ?string $formattedMessage = null,
        ?\Exception $previous = null
    );

    /**
     * Get the path to the file that caused the compilation error.
     *
     * @return - The path to the source file.
     */
    public function getSourceFile(): ?string;

    /**
     * Get the line in the file that caused the compilation error.
     *
     * @return - The number of the line that caused the error.
     */
    public function getSourceLine(): ?int;

    /**
     * Get the column in the file that caused the compilation error.
     *
     * @return - The number of the column that caused the error.
     */
    public function getSourceColumn(): ?int;

    /**
     * Get the complete formatted message that shows the exact position
     * of the compilation error.
     *
     * @return - The complete formatted error message.
     */
    public function getFormattedMessage(): ?string;

    /**
     * Format the exception as a string for display.
     *
     * @return - This exception as a string.
     */
    public function __toString(): string;
}

/**
 * Represents a container for a single custom import.
 * It can hold the filename, the scss source string and a map definition.
 */
class SassImport {

    /**
     * Set the path of the file to import.
     * If no scss source is provided libsass will load this file.
     * Make sure the file exists and is readable. Relative paths are
     * supported.
     * If no path is provided but a scss source is a fake pathname
     * will be generated.
     *
     * @param $path - The path to the scss file.
     *
     * @return - A shallow copy of the current `SassImport`.
     */
    public function setPath(string $path): this;

    /**
     * Get the path of the file that will be imported.
     *
     * @return - The path of the scss file.
     */
    public function getPath(): string;

    /**
     * Set the scss source.
     * If an empty string is provided and a file path is
     * specified the file will not be loaded. To make sure the
     * file will be loaded set the source to `null`.
     *
     * @param $source - The scss source or `null` to make libsass
     *                  load a file.
     *
     * @return - A shallow copy of the current `SassImport`.
     */
    public function setSource(?string $source): this;

    /**
     * Get the scss source that will be imported and compiled.
     *
     * @return - The scss source.
     */
    public function getSource(): ?string;

    /**
     * Set the source map for the import.
     * It will be used to re-map the actual sourcemap with the provided ones.
     * The map has to be a valid json string.
     *
     * NOTE: The source map will be passed to libsass, but libsass
     *       currently does not do anything with it yet.
     *
     * @param $map - The source map or `null`.
     *
     * @return - A shallow copy of the current `SassImport`.
     */
    public function setSrcMap(?string $map): this;

    /**
     * Get the source map that will be imported and used to re-map
     * the actual sourcemap.
     *
     * NOTE: See the note in the description of `setSrcMap()`.
     *
     * @return - The source map.
     */
    public function getSrcMap(): ?string;
}

}


namespace Sass\Types {

/**
 * The base class for all `SassValue`s.
 */
abstract class SassValue
{
    /**
     * Check if this `SassValue` equals another `SassValue`.
     *
     * @param $value - The `SassValue` to compare with.
     *
     * @return - `true` when it matches, `false` otherwise.
     */
    abstract public function equals(SassValue $value): bool;

    /**
     * Return a string representation of this `SassValue`.
     *
     * @return string
     */
    abstract public function __toString(): string;

    /**
     * A helper method to convert the most common scalars
     * to `SassValue` instances.
     * If the conversion fails an exception is thrown.
     *
     * @param $value - The value to convert
     *
     * @return - The wrapped value as `SassValue` instance.
     */
    final public static function cs(mixed $value): SassValue;
}

/**
 * Defines methods needed for validating `SassValue`s.
 */
interface SassNeedsValidation
{
    /**
     * Check if the `SassValue` implementing this interface is valid.
     *
     * @param $seen - A list with all visited elements
     *
     * @return bool
     */
    public function isValid(Vector<SassValue> $seen = Vector {}): bool;
}

/**
 * A wrapper for null.
 */
class SassNull extends SassValue
{
    /**
     * Check if this `SassNull` equals another `SassNull`.
     *
     * @param $value - The `SassNull` to compare with.
     *
     * @return - `true` when it matches, `false` otherwise.
     */
    final public function equals(SassValue $value): bool;

    /**
     * Return a string representation of this `SassNull`.
     *
     * @return string
     */
    public function __toString(): string;
}

/**
 * A wrapper for numeric values.
 */
class SassNumber extends SassValue
{

    /**
     * Get the value of the number.
     *
     * @return - The value.
     */
    final public function getValue(): num;

    /**
     * Set the value of the number.
     *
     * @param $unit - The new unit.
     * @param $unit - The new unit (optional).
     *
     * @return - A shallow copy of the current `SassNumber`.
     */
    final public function setValue(num $value, ?string $unit = null): this;

    /**
     * Get the unit of the number.
     *
     * @return - The unit.
     */
    final public function getUnit(): string;

    /**
     * Set the unit of the number.
     *
     * @param $unit - The new unit.
     *
     * @return - A shallow copy of the current `SassNumber`.
     */
    final public function setUnit(string $unit): this;

    /**
     * Check if this `SassNumber` equals another `SassNumber`.
     *
     * @param $value - The `SassNumber` to compare with.
     *
     * @return - `true` when it matches, `false` otherwise.
     */
    final public function equals(SassValue $value): bool;

    /**
     * Return a string representation of this `SassNumber`.
     *
     * @return string
     */
    public function __toString(): string;
}

/**
 * A wrapper for string values.
 */
class SassString extends SassValue
{

    /**
     * Get the wrapped string.
     *
     * @return - The wrapped string.
     */
    final public function getValue(): string;

    /**
     * Set the wrapped string.
     *
     * @param $value - The new string.
     *
     * @return - A shallow copy of the current `SassString`.
     */
    final public function setValue(string $value): this;

    /**
     * Returns whether the string is quoted or not.
     *
     * @return - `true` if the string is quoted,
     *           `false` otherwise.
     */
    final public function isQuoted(): bool;

    /**
     * Unquote the string.
     *
     * @return - A shallow copy of the current `SassString`.
     */
    public function quote(): this;

    /**
     * Quote the string.
     *
     * @return - A shallow copy of the current `SassString`.
     */
    public function unquote(): this;

    /**
     * Check if this string needs quotes (eg. not starting with a letter or
     * containing spaces).
     *
     * @return - `true` when spaces are needed; `false` otherwise.
     */
    public function needsQuotes(): bool;

    /**
     * Quote the string only if it is necessary.
     *
     * @return - A shallow copy of the current `SassString`.
     */
    public function autoQuote(): this;

    /**
     * Check if this `SassString` equals another `SassString`.
     *
     * @param $value - The `SassString` to compare with.
     *
     * @return - `true` when it matches, `false` otherwise.
     */
    final public function equals(SassValue $value): bool;

    /**
     * Return a string representation of this `SassString`.
     *
     * @return string
     */
    public function __toString(): string;
}

/**
 * A wrapper for boolean values.
 */
class SassBoolean extends SassValue
{

    /**
     * Get the wrapped boolean.
     *
     * @return - The boolean value of this object.
     */
    final public function getValue(): bool;

    /**
     * Set the wrapped boolean.
     *
     * @param $value - The new boolean value.
     *
     * @return - A shallow copy of the current `SassBoolean`.
     */
    final public function setValue(bool $value): this;

    /**
     * Check if this `SassBoolean` equals another `SassBoolean`.
     *
     * @param $value - The `SassBoolean` to compare with.
     *
     * @return - `true` when it matches, `false` otherwise.
     */
    final public function equals(SassValue $value): bool;

    /**
     * Return a string representation of this `SassBoolean`.
     *
     * @return string
     */
    public function __toString(): string;
}



/**
 * A shape to hold a RGB color representation
 */
type SassColorRGB = shape('r' => int, 'g' => int, 'b' => int);

/**
 * A shape to hold a HSL color representation
 */
type SassColorHSL = shape('h' => float, 's' => float, 'l' =>  float);

/**
 * A color representation. The color is handled in the RGB colorspace
 * internally. Helper methods for working with the HSL colorspace
 * are available.
 */
class SassColor extends SassValue
{

    /**
     * Set the alpha channel's value of this color
     *
     * @param $alpha - The value of the alpha channel
     *                 (value in the range of 0 to 1, optional).
     *
     * @return - A shallow copy of the current `SassColor`.
     */
    final public function setAlpha(float $a): this;

    /**
     * Get the alpha channel's value of this color.
     *
     * @return - The value of the alpha channel.
     */
    final public function getAlpha(): float;

    /**
     * Set this colors properties by providing color information
     * as RGB representation.
     *
     * @param $r - The red color value as int between 0 and 255.
     * @param $g - The green color value as int between 0 and 255.
     * @param $b - The blue color value as int between 0 and 255.
     * @param $alpha - The value of the alpha channel
     *                 (value in the range of 0 to 1, optional).
     *
     * @return - A shallow copy of the current `SassColor`.
     */
    final public function setRGB(
        int $r,
        int $g,
        int $b,
        ?float $alpha = null
    ): this;

    /**
     * Set this colors properties by providing a shape containing
     * color information as RGB representation.
     *
     * @param $c - A RGB representation
     * @param $alpha - The value of the alpha channel
     *                 (value in the range of 0 to 1, optional).
     *
     * @return - A shallow copy of the current `SassColor`.
     */
    final public function setRGBFromShape(
        SassColorRGB $c,
        ?float $alpha = null
    ): this;

    /**
     * Returns the current color as RGB representation.
     *
     * @return - The RGB representation as shape.
     */
    final public function getRGB(): SassColorRGB;

    /**
     * Set this colors properties by providing color information
     * as HSL representation.
     *
     * @param $h - The hue as number between 0 to 360.
     * @param $s - The saturation as float between 0 and 1.
     * @param $l - The lightness as float between 0 and 1.
     * @param $alpha - The value of the alpha channel
     *                 (value in the range of 0 to 1, optional).
     *
     * @return - A shallow copy of the current `SassColor`.
     */
    final public function setHSL(
        float $h,
        float $s,
        float $l,
        ?float $alpha = null
    ): this;

    /**
     * Set this colors properties by providing a shape containing
     * color information as HSL representation.
     *
     * @param $c - A HSL representation
     * @param $alpha - The value of the alpha channel
     *                 (value in the range of 0 to 1, optional).
     *
     * @return - A shallow copy of the current `SassColor`.
     */
    final public function setHSLFromShape(
        SassColorHSL $c,
        ?float $alpha = null
    ): this;

    /**
     * Returns the current color as HSL representation.
     *
     * @return - The HSL representation as shape.
     */
    final public function getHSL(): SassColorHSL;

    /**
     * Converts an RGB color to HSL. Conversion formula
     * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
     *
     * @param $r - The red color value as int between 0 and 255.
     * @param $g - The green color value as int between 0 and 255.
     * @param $b - The blue color value as int between 0 and 255.
     * @return - The HSL representation as shape.
     *           h is in the range of 0 - 360, s and l in the range of 0 - 1.
     */
    final public static function rgbToHsl(int $r, int $g, int $b): SassColorHSL;

    /**
     * Converts an HSL color to RGB. Conversion formula
     * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
     *
     * @param $h - The hue as number between 0 to 360.
     * @param $s - The saturation as float between 0 and 1.
     * @param $l - The lightness as float between 0 and 1.
     * @return - The RGB representation as shape with each color channel
     *           in the range of 0 - 255.
     */
    final public static function hslToRgb(
        num $h,
        float $s,
        float $l
    ): SassColorRGB;

    /**
     * Check if this `SassColor` equals another `SassColor`.
     *
     * @param $value - The `SassColor` to compare with.
     *
     * @return - `true` when all color channels match, `false`
     *           otherwise.
     */
    final public function equals(SassValue $value): bool;

    /**
     * Return a string representation of this `SassColor`.
     *
     * @return string
     */
    public function __toString(): string;
}

/**
 * The base interface for all `SassCollection`s.
 */
interface SassCollection<Tk, Te> extends
    \KeyedContainer<Tk,SassValue>,
    \Indexish<Tk,SassValue>,
    \IteratorAggregate<SassValue>
{
    /**
     * Checks if the current `SassCollection` is empty.
     *
     * @return - `true` if the current `SassCollection` is empty; `false`
     *           otherwise.
     */
    public function isEmpty(): bool;

    /**
     * Provides the number of elements in current `SassCollection`.
     *
     * @return - The number of elements in current `SassCollection`.
     */
    public function count(): int;

    /**
     * Returns the value at the specified key in the current `SassCollection`.
     *
     * If the key is not present, an exception is thrown. If you don't want an
     * exception to be thrown, use `get()` instead.
     *
     * @param $k - the key from which to retrieve the value.
     *
     * @return - The value at the specified key; or an exception if the key
     *           does not exist.
     */
    public function at(Tk $k): SassValue;

    /**
     * Returns the value at the specified key in the current `SassCollection`.
     *
     * If the key is not present, null is returned. If you would rather have an
     * exception thrown when a key is not present, then use `at()`.
     *
     * @param $k - the key from which to retrieve the value.
     *
     * @return - The value at the specified key; or `null` if the key does not
     *           exist.
     */
    public function get(Tk $k): ?SassValue;

    /**
     * Determines if the specified key is in the current `SassCollection`.
     *
     * @return - `true` if the specified key is present in the current
     *           `SassCollection`; returns `false` otherwise.
     */
    public function containsKey(Tk $k): bool;

    /**
     * Removes the specified key (and associated value) from the current
     * collection.
     *
     * If the key is not in the current collection, the current collection is
     * unchanged.
     *
     * @param $k - The key to remove.
     *
     * @return - A shallow copy of the current `SassCollection`.
     */
    public function removeKey(Tk $k): this;

    /**
     * Stores a value into the current `SassCollection` with the specified key,
     * overwriting the previous value associated with the key.
     *
     * @param $k - The key to which we will set the value.
     * @param $v - The value to set.
     *
     * @return - A shallow copy of the current `SassCollection`.
     */
    public function set(Tk $k, SassValue $v): this;

    /**
     * For every element in the provided `Traversable`, stores a value into the
     * current collection associated with each key, overwriting the previous
     * value associated with the key.
     *
     * @param $traversable - The `Traversable` with the new values to set. If
     *                       `null` is provided, no changes are made.
     *
     * @return - A shallow copy of the current `SassCollection`.
     */
    public function setAll(?KeyedTraversable<Tk, SassValue> $traversable): this;

    /**
     * Add a value to the collection and return the collection itself.
     *
     * @param $v - The value to add.
     *
     * @return - A shallow copy of the current `SassCollection`.
     */
    public function add(Te $v): this;

    /**
     * For every element in the provided `Traversable`, append a value into the
     * current collection.
     *
     * @param $k - The `Traversable` with the new values to set. If `null` is
     *             provided, no changes are made.
     *
     * @return - A shallow copy of the current `SassCollection`.
     */
    public function addAll(?Traversable<Te> $it): this;

    /**
     * Remove all the elements from the current `SassCollection`.
     *
     * @return - A shallow copy of the current `SassCollection`.
     */
    public function clear(): this;
}

class SassList extends SassValue implements
    SassNeedsValidation,
    SassCollection<int,SassValue>
{
    const string SEPARATOR_COMMA = ',';
    const string SEPARATOR_SPACE = ' ';

    /**
     * Clone all the elements in this new `SassList` because the original one
     * and this one share the same elements.
     */
    final public function __clone();

    /**
     * Set the separator of this `SassList`.
     * Available separators are:
     *     - `SassList::SEPARATOR_COMMA`
     *     - `SassList::SEPARATOR_SPACE`
     *
     * @param $separator - The new $separator
     *
     * @return - A shallow copy of the current `SassList`.
     */
    final public function setSeparator(string $separator): this;

    /**
     * Gets the currently selected seperator.
     *
     * @return - The separator
     */
    final public function getSeparator(): string;

    /**
     * Returns the first value in the current `SassList`.
     *
     * @return - The first value in the current `SassList`, or `null` if the
     *           `SassList` is empty.
     */
    final public function firstValue(): ?SassValue;

    /**
     * Returns the first key in the current `SassList`.
     *
     * @return - The first key (an integer) in the current `SassList`, or
     *           `null` if the `SassList` is empty.
     */
    final public function firstKey(): ?int;

    /**
     * Returns the last value in the current `SassList`.
     *
     * @return - The last value in the current `SassList`, or `null` if the
     *           current `SassList` is empty.
     */
    final public function lastValue(): ?SassValue;

    /**
     * Returns the last key in the current `SassList`.
     *
     * @return - The last key (an integer) in the current `SassList`, or
     *           `null` if the `SassList` is empty.
     */
    final public function lastKey(): ?int;

    /**
     * Checks if the current `SassList` is empty.
     *
     * @return - `true` if the current `SassList` is empty; `false` otherwise.
     */
    final public function isEmpty(): bool;

    /**
     * Provides the number of elements in current `SassList`.
     *
     * @return - The number of elements in current `SassList`.
     */
    final public function count(): int;

    /**
     * Returns the value at the specified key in the current `SassList`.
     *
     * If the key is not present, an exception is thrown. If you don't want an
     * exception to be thrown, use `get()` instead.
     *
     * @param $k - the key from which to retrieve the value.
     *
     * @return - The value at the specified key; or an exception if the key
     *           does not exist.
     */
    final public function at(int $k): SassValue;

    /**
     * Returns the value at the specified key in the current `SassList`.
     *
     * If the key is not present, null is returned. If you would rather have an
     * exception thrown when a key is not present, then use `at()`.
     *
     * @param $k - the key from which to retrieve the value.
     *
     * @return - The value at the specified key; or `null` if the key does not
     *           exist.
     */
    final public function get(int $k): ?SassValue;

    /**
     * Stores a value into the current `SassList` with the specified key,
     * overwriting the previous value associated with the key.
     *
     * If the key is not present, an exception is thrown. If you want to add
     * a value even if a key is not present, use `add()`.
     *
     * @param $k - The key to which we will set the value.
     * @param $v - The value to set.
     *
     * @return - A shallow copy of the current `SassList`.
     */
    final public function set(int $k, SassValue $v): this;

    /**
     * For every element in the provided `Traversable`, stores a value into the
     * current `SassList` associated with each key, overwriting the previous
     * value associated with the key.
     *
     * If a key is not present the current `SassList` that is present in the
     * `Traversable`, an exception is thrown. If you want to add a value even
     * if a key is not present, use `addAll()`.
     *
     * @param $k - The `Traversable` with the new values to set. If `null` is
     *             provided, no changes are made.
     *
     * @return - A shallow copy of the current `SassList`.
     */
    final public function setAll(?KeyedTraversable<int,SassValue> $it): this;

    /**
     * Remove all the elements from the current `SassList`.
     *
     * @return - A shallow copy of the current `SassList`.
     */
    final public function clear(): this;

    /**
     * Determines if the specified key is in the current `SassList`.
     *
     * @return - `true` if the specified key is present in the current
     *           `SassList`; returns `false` otherwise.
     */
    final public function containsKey(int $k): bool;

    /**
     * Append a copy of a value to the end of the current `SassList`, assigning
     * the next available integer key.
     *
     * If you want to overwrite a value, use `set()`.
     *
     * @param $v - The value to set to the newly appended key
     *
     * @return - A shallow copy of the current `SassList`.
     */
    final public function add(SassValue $v): this;

    /**
     * For every element in the provided `Traversable`, append a value into
     * this `SassList`, assigning the next available integer key for each.
     *
     * If you want to overwrite values, use `setAll()`.
     *
     * @param $k - The `Traversable` with the new values to set. If `null` is
     *             provided, no changes are made.
     *
     * @return - A shallow copy of the current `SassList`.
     */
    final public function addAll(?Traversable<SassValue> $it): this;

    /**
     * Removes the specified key from the current `SassList`.
     *
     * This will cause elements with higher keys to be renumbered by `n - 1`,
     * where n is the last key in the current `SassList`.
     *
     * @param $k - The key to remove.
     *
     * @return - A shallow copy of the current `SassList`.
     */
    final public function removeKey(int $k): this;

    /**
     * Remove the last element of the current `SassList` and return it.
     *
     * This function throws an exception if this `SassList` is empty.
     *
     * This `SassList` will have `n - 1` elements after this operation.
     *
     * @return - The value of the last element.
     */
    final public function pop(): SassValue;

    /**
     * Returns an iterator that points to beginning of the current `SassList`.
     *
     * @return - A `KeyedIterator` that allows you to traverse the current
     *           `SassList`.
     */
    final public function getIterator(): \KeyedIterator<int, SassValue>;

    /**
     * Reverse the elements of the current `SassList` in place.
     *
     * @return - A shallow copy of the current `SassList`.
     */
    final public function reverse(): this;

    /**
     * Shuffles the values of the current `SassList` randomly in place.
     *
     * @return - A shallow copy of the current `SassList`.
     */
    final public function shuffle(): this;

    /**
     * Check if this `SassList` equals another `SassList`.
     *
     * @param $value - The `SassList` to compare with.
     *
     * @return - `true` when all values and their order matches, `false`
     *           otherwise.
     */
    final public function equals(SassValue $value): bool;

    /**
     * Check if this `SassList` is valid.
     * Valid `SassList`s may not contain recursing elements.
     *
     * @param $seen - A list with all visited elements
     *
     * @return bool
     */
    final public function isValid(Vector<SassValue> $seen = Vector {}): bool;

    /**
     * Return a string representation of this `SassList`.
     *
     * @return string
     */
    public function __toString(): string;

    public function __debugInfo(): array;
}

/**
 * `SassMapPair` is a container to hold the key and value for a key-value
 * pair of a `SassMap`. The key and value are of type `SassValue` but
 * `SassMapPair` is not a `SassValue` itsself.
 * `SassMapPair` is not a collection like HHVM's `Pair`.
 */
class SassMapPair
{

    /**
     * Set both the key and value at once. Uses `setKey()` and `setValue()`
     * internally.
     *
     * @param $k - The key
     * @param $v - The value
     *
     * @return - A shallow copy of the current `SassMapPair`.
     */
    final public function set(mixed $k, mixed $v): this;

    /**
     * Get the key of this pair.
     *
     * @return - The key.
     */
    public function getKey(): SassValue;

    /**
     * Set the key of this `SassMapPair`.
     *
     * @param $k - The key. If it is not a `SassValue`
     *             it will be tried to convert it to one.
     *             If that fails an exception is thrown.
     *
     * @return - A shallow copy of the current `SassMapPair`.
     */
    final public function setKey(mixed $k): this;

    /**
     * Get the value of this pair.
     *
     * @return - The key.
     */
    public function getValue(): SassValue;

    /**
     * Set the value of this `SassMapPair`.
     *
     * @param $k - The value. If it is not a `SassValue`
     *             it will be tried to convert it to one.
     *             If that fails an exception is thrown.
     *
     * @return - A shallow copy of the current `SassMapPair`.
     */
    final public function setValue(mixed $v): this;

    /**
     * Creates a `SassMap` out of this key-value pair.
     * The resulting map only contains this pair and
     * has a count of one.
     *
     * @return - A new `SassMap` containing this pair.
     */
    final public function toMap(): SassMap;
}

/**
 * `SassMap` is an ordered dictionary-style collection.
 *
 * `SassMap`s preserve insertion order of key/value pairs. When iterating over
 *  a`SassMap`, the key/value pairs appear in the order they were inserted.
 *
 * `SassMap`s only support all `SassValues` (even `SassList` or another
 * `SassMap`) as keys.
 */
class SassMap extends SassValue implements
    SassNeedsValidation,
    SassCollection<SassValue,SassMapPair>
{

    /**
     * Clone all the elements in this new `SassMap` because the original one
     * and this one share the same elements.
     */
    final public function __clone(): void;

    /**
     * Returns a `SassList` containing the values of the current `SassMap`.
     *
     * @return - a `SassList` containing the values of the current `SassMap`.
     */
    final public function values(): SassList;

    /**
     * Returns a `SassList` containing the keys of the current `SassMap`.
     *
     * @return - a `SassList` containing the keys of the current `SassMap`.
     */
    final public function keys(): SassList;

    /**
     * Returns the first value in the current `SassMap`.
     *
     * @return - The first value in the current `SassMap`,
     *           or `null` if the `SassMap` is empty.
     */
    final public function firstValue(): ?SassValue;

    /**
     * Returns the first key in the current `SassMap`.
     *
     * @return - The first key in the current `SassMap`,
     *           or `null` if the `SassMap` is empty.
     */
    final public function firstKey(): ?SassValue;

    /**
     * Returns the last value in the current `SassMap`.
     *
     * @return - The last value in the current `SassMap`,
     *           or `null` if the `SassMap` is empty.
     */
    final public function lastValue(): ?SassValue;

    /**
     * Returns the last key in the current `SassMap`.
     *
     * @return - The last key in the current `SassMap`,
     *           or `null` if the `SassMap` is empty.
     */
    final public function lastKey(): ?SassValue;

    /**
     * Checks if the current `SassMap` is empty.
     *
     * @return - `true` if the current `SassMap` is empty; `false` otherwise.
     */
    final public function isEmpty(): bool;

    /**
     * Provides the number of elements in the current `SassMap`.
     *
     * @return - The number of elements in the current `SassMap`.
     */
    final public function count(): int;

    /**
     * Returns the value at the specified key in the current `SassMap`.
     *
     * If the key is not present, an exception is thrown. If you don't want an
     * exception to be thrown, use `get()` instead.
     *
     * @param $k - the key from which to retrieve the value.
     *
     * @return - The value at the specified key; or an exception if the key
     *           does not exist.
     */
    final public function at(SassValue $k): SassValue;

    /**
     * Returns the value at the specified key in the current `SassMap`.
     *
     * If the key is not present, `null` is returned. If you would rather have
     * an exception thrown when a key is not present, then use `at()`.
     *
     * @param $k - the key from which to retrieve the value.
     *
     * @return - The value at the specified key; or `null` if the key does not
     *           exist.
     */
    final public function get(SassValue $k): ?SassValue;

    /**
     * Stores a value into the current `SassMap` with the specified key,
     * overwriting the previous value associated with the key.
     *
     * This method is equivalent to `SassMap::add()`. If the key to set does
     * not exist, it is created. This is inconsistent with, for example,
     * `SassList::set()` where if the key is not found, an exception is thrown.
     *
     * @param $k - The key to which we will set the value.
     * @param $v - The value to set.
     *
     * @return - A shallow copy of the current `SassMap`.
     */
    final public function set(SassValue $k, SassValue $v): this;

    /**
     * For every element in the provided `Traversable`, stores a value into
     * the current SassMap associated with each key, overwriting the previous
     * value associated with the key.
     *
     * This method is equivalent to `SassMap::addAll()`. If a key to set does
     * not exist in the `SassMap` that does exist in the `Traversable`, it is
     * created. This is inconsistent with, for example, `SassList::set()` where
     * if the key is not found an exception is thrown.
     *
     * @param $traversable - The `Traversable` with the new values to set. If
     *                       `null` is provided, no changes are made.
     *
     * @return - A shallow copy of the current `SassMap`.
     */
    final public function setAll(
        ?KeyedTraversable<SassValue, SassValue> $traversable
    ): this;

    /**
     * Remove all the elements from the current `SassMap`.
     *
     * @return - A shallow copy of the current `SassMap`.
     */
    final public function clear(): this;

    /**
     * Determines if the specified key is in the current `SassMap`.
     *
     * The method is interchangeable with `contains()`.
     *
     * @param $k - The key to check.
     *
     * @return - `true` if the specified key is present in the current
     *           `SassMap`; returns `false` otherwise.
     */
    final public function containsKey(SassValue $k): bool;

    /**
     * Determines if the specified key is in the current `SassMap`.
     *
     * The method is interchangeable with `containsKey()`.
     *
     * @param $k - The key to check.
     *
     * @return - `true` if the specified key is present in the current
     *           `SassMap`; returns `false` otherwise.
     */
    final public function contains(SassValue $k): bool;

    /**
     * Add a key/value pair to the end of the current `SassMap`.
     *
     * This method is equivalent to `SassMap::set()`. If the key in the
     * `SassMapPair` exists in the `SassMap`,  the value associated with it is
     * overwritten.
     *
     * @param $p - The key/value SassMapPair to add to the current `SassMap`.
     *
     * @return - A shallow copy of the current `SassMap`.
     */
    final public function add(SassMapPair $p): this;

    /**
     * For every element in the provided `Traversable`, add a key/value pair
     * into the current `SassMap`.
     *
     * This method is equivalent to `SassMap::setAll()`. If a key in the
     * `Traversable` exists in the `SassMap`, then the value associated with
     * that key in the `SassMap` is overwritten.
     *
     * @param $k - The `Traversable` with the new key/value `Pair` to set. If
     *             `null` is provided, no changes are made.
     *
     * @return - A shallow copy of the current `SassMap`.
     */
    final public function addAll(?Traversable<SassMapPair> $it): this;

    /**
     * Removes the specified key (and associated value) from the current
     * `SassMap`.
     *
     * This method is interchangeable with `remove()`.
     *
     * @param $k - The key to remove.
     *
     * @return - A shallow copy of the current `SassMap`.
     */
    final public function removeKey(SassValue $k): this;

    /**
     * Removes the specified key (and associated value) from the current
     * `SassMap`.
     *
     * This method is interchangeable with `removeKey()`.
     *
     * @param $k - The key to remove.
     *
     * @return - A shallow copy of the current `SassMap`.
     */
    final public function remove(SassValue $k): this;

    /**
     * Returns an iterator that points to beginning of the current `SassMap`.
     *
     * @return - A `KeyedIterator` that allows you to traverse the current
     *           `SassMap`.
     */
    final public function getIterator(): \KeyedIterator<SassValue,SassValue>;

    /**
     * Check if this `SassMap` equals another `SassMap`.
     *
     * @param $value - The `SassMap` to compare with.
     *
     * @return - `true` when all keys and values and their order matches,
     *           `false` otherwise.
     */
    final public function equals(SassValue $value): bool;

    /**
     * Check if this `SassMap` is valid.
     * Valid `SassMap`s may not contain recursing elements.
     *
     * @param $seen - A list with all visited elements
     *
     * @return bool
     */
    final public function isValid(Vector<SassValue> $seen = Vector {}): bool;

    /**
     * Return a string representation of this `SassMap`.
     *
     * @return string
     */
    public function __toString(): string;

    public function __debugInfo(): array;

}

/**
 * Iterator to traverse a `SassMap`.
 * Will be returned by `SassMap`::`getIterator()`.
 */
final class SassMapIterator implements \KeyedIterator<SassValue,SassValue>
{

    public function __construct(Map<string, SassMapPair> $map);

    public function current(): SassValue;

    public function key(): SassValue;

    public function valid(): bool;

    public function next(): void;

    public function rewind(): void;
}

/**
 * Class for generating errors in custom functions in libsass
 */
class SassError extends SassValue
{

    /**
     * Set the new message.
     *
     * @param $message - The new message.
     *
     * @return - A shallow copy of the current `SassError`.
     */
    final public function setMessage(string $message): this;

    /**
     * Get the current message
     *
     * @return - The message
     */
    final public function getMessage(): string;

    /**
     * Check if this `SassError` equals another `SassError`.
     *
     * @param $value - The `SassError` to compare with.
     *
     * @return - `true` when it matches, `false` otherwise.
     */
    final public function equals(SassValue $value): bool;

    /**
     * Convert the error to a string representation.
     *
     * @return string
     */
    public function __toString(): string;
}

/**
 * Class for generating warnings in custom functions in libsass
 */
class SassWarning extends SassValue
{

    /**
     * Set the new message.
     *
     * @param $message - The new message.
     *
     * @return - A shallow copy of the current `SassWarning`.
     */
    final public function setMessage(string $message): this;

    /**
     * Get the current message
     *
     * @return - The message
     */
    final public function getMessage(): string;

    /**
     * Check if this `SassWarning` equals another `SassWarning`.
     *
     * @param $value - The `SassWarning` to compare with.
     *
     * @return - `true` when it matches, `false` otherwise.
     */
    final public function equals(SassValue $value): bool;

    /**
     * Convert the warning to a string representation.
     *
     * @return string
     */
    public function __toString(): string;
}

}
