<?hh

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
    /*[[__Native
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
    ]]*/

    private array<string> $includePaths = [];
    private int $precision = 5;
    private int $style = self::STYLE_NESTED;
    private int $syntax = self::SYNTAX_SCSS;
    private bool $sourceComments = false;
    private ?string $linefeed = null;
    private ?string $indent = null;
    private bool $embedMap = false;
    private ?string $sourceRoot = null;
    private array<string,mixed> $userFunctions = [];
    private array<string,array<string,mixed>> $userImporters = [];
    private array<string,array<string,mixed>> $headers = [];

    /**
     * Complile a scss string to css.
     *
     * @param $source - String containing valid scss source code.
     *
     * @throws SassException - If the source contains errors.
     *
     * @return - The compiled css code.
     */
    <<__Native>>
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
    <<__Native>>
    final public function compileWithMap(string $source, string $mapFileName): array;

    <<__Native>>
    final private function compileFileNative(string $fileName): string;

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
    final public function compileFile(string $fileName): string
    {
        if (empty($fileName)) {
            throw new \RuntimeException(
                'The file name may not be empty.', 54551040
            );
        }
        // Make the file path absolute
        if (substr($fileName, 0, 1) !== '/') {
            $fileName = getcwd().'/'.$fileName;
        }
        if (!file_exists($fileName) || !is_readable($fileName)) {
            throw new \RuntimeException(
                'The file can not be read.', 54551041
            );
        }
        return $this->compileFileNative($fileName);
    }

    <<__Native>>
    final private function compileFileWithMapNative(
        string $fileName,
        string $mapFileName
    ): array;

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
    ): SassResonse {
        if (empty($fileName)) {
            throw new \RuntimeException(
                'The file name may not be empty.', 54551040
            );
        }
        // Make  the file path absolute
        if (substr($fileName, 0, 1) !== '/') {
            $fileName = getcwd().'/'.$fileName;
        }
        if (!file_exists($fileName) || !is_readable($fileName)) {
            throw new \RuntimeException(
                'The file can not be read.', 54551041
            );
        }
        $mapFileName = empty($mapFileName) ? $fileName.'.map' : $mapFileName;

        $response = $this->compileFileWithMapNative($fileName, $mapFileName);

        return shape('css' => $response['css'], 'map' => $response['map']);
    }

    /**
     * Get the currently used formatting style. Default is Sass::STYLE_NESTED.
     *
     * @return int
     */
    public function getStyle(): int
    {
        return $this->style;
    }

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
    final public function setStyle(int $style): this
    {
        if (!in_array($style, array (
            self::STYLE_NESTED, self::STYLE_EXPANDED,
            self::STYLE_COMPACT, self::STYLE_COMPRESSED
        ))) {
            throw new \InvalidArgumentException(
                'This style is not supported.', 54551120
            );
        }
        $this->style = $style;
        return $this;
    }

    /**
     * Get the currently used syntax type. Default is Sass::SYNTAX_SCSS.
     *
     * @return - The currently used syntax type.
     */
    public function getSyntax(): int
    {
        return $this->syntax;
    }

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
    final public function setSyntax(int $syntax): this
    {
        if (!in_array($syntax, array (
            self::SYNTAX_SCSS, self::SYNTAX_SASS
        ))) {
            throw new \InvalidArgumentException(
                'This syntax is not supported.', 54551121
            );
        }
        $this->syntax = $syntax;
        return $this;
    }

    /**
     * Gets the currently used include paths where the compiler will search
     * for files to include.
     *
     * @return - A vector containing all additional include paths.
     */
    public function getIncludePaths(): ImmVector<string>
    {
        return new ImmVector($this->includePaths);
    }

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
    final public function addIncludePath(string $includePath): this
    {
        // Make the file path absolute
        if (substr($includePath, 0, 1) !== '/') {
            $includePath = getcwd().'/'.$includePath;
        }
        if (!is_dir($includePath) || !is_readable($includePath)) {
            throw new \RuntimeException(
                'The path '.$includePath.' does not exist or is not readable',
                54551042
            );
        }
        $this->includePaths[] = $includePath;
        return $this;
    }

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
    final public function setIncludePaths(Traversable<string> $includePaths): this
    {
        $this->includePaths = array();
        foreach ($includePaths as $idx => $includePath) {
            $this->addIncludePath($includePath);
        }
        return $this;
    }

    /**
     * Get the currently used precision for decimal numbers.
     *
     * @return - Number of digits of the fractional part.
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * Set the precision that will be used for decimal numbers.
     *
     * @param $precision - Number of digits for the fractional part.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setPrecision(int $precision): this
    {
        if ($precision < 0) {
            throw new \InvalidArgumentException(
                'The precision has to be greater or equal than 0.', 54551122
            );
        }
        $this->precision = $precision;
        return $this;
    }

    /**
     * Returns whether the compiled css files will contain comments indicating
     * the corresponding source line.
     *
     * @return - `true` when source line comments will be emitted; `false`
     *           otherwise.
     */
    public function getIncludesSourceComments(): bool
    {
        return $this->sourceComments;
    }

    /**
     * Sets whether to enable emitting comments in the generated CSS indicating
     * the corresponding source line or not.
     *
     * @param $sourceComments - Pass `true` to enable emmiting source line
     *                          comments; `false` otherwise.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setIncludesSourceComments(bool $sourceComments): this
    {
        $this->sourceComments = $sourceComments;
        return $this;
    }

    /**
     * Alias of self::getIncludesSourceComments()
     */
    public function includesSourceComments(): bool
    {
        return $this->getIncludesSourceComments();
    }

    /**
     * Alias of self::includeSourceComments()
     */
    final public function includeSourceComments(bool $sourceComments): this
    {
        return $this->setIncludesSourceComments($sourceComments);
    }

    /**
     * Get the string that will be used for line feeds in the compiled CSS.
     * If null is returned `libsass`' default will be used.
     *
     * @return - The currently used line feed string.
     */
    public function getLinefeed(): ?string
    {
        return $this->linefeed;
    }

    /**
     * Set the string to be used to for line feeds in the compiled CSS.
     * Pass null if you want to use the default from `libsass`.
     *
     * @param $linefeed - The new line feed string.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setLinefeed(?string $linefeed): this
    {
        $this->linefeed = $linefeed;
        return $this;
    }

    /**
     * Get the string that will be used for indentation in the compiled CSS.
     * If null is returned `libsass`' default will be used.
     *
     * @return - The currently used indentation string.
     */
    public function getIndent(): ?string
    {
        return $this->indent;
    }

    /**
     * Set the string to be used to for indentation in the compiled CSS.
     * Pass null if you want to use the default from `libsass`.
     *
     * @param $indent - The new indentation string.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setIndent(?string $indent): this
    {
        $this->indent = $indent;
        return $this;
    }

    /**
     * Gets the information whether the the source mapping is embedded in
     * the compiled CSS as data uri.
     *
     * @return - `true` if the source mapping is embedded in the compiled CSS
     *           as data uri; `false` otherwise
     */
    public function getEmbedMap(): bool
    {
        return $this->embedMap;
    }

    /**
     * Control if the source mapping is embedded in the compiled CSS as data
     * uri.
     *
     * @param $embedMap - Whether the source mapping will be embedded or not.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setEmbedMap(bool $embedMap): this
    {
        $this->embedMap = $embedMap;
        return $this;
    }

    /**
     * Alias of self::getEmbedMap()
     */
    public function isMapEmbedded(): bool
    {
        return $this->getEmbedMap();
    }

    /**
     * Alias of self::setEmbedMap()
     */
    final public function embedMap(bool $embedMap): this
    {
        return $this->setEmbedMap($embedMap);
    }

    /**
     * Get the pass-through for the sourceRoot property in source maps.
     * If null is returned the sourceRoot property is not populated.
     *
     * @return - The value for the sourceRoot property.
     */
    public function getSourceRoot(): ?string
    {
        return $this->sourceRoot;
    }

    /**
     * Set the pass-through for the sourceRoot property in source maps.
     * Pass null if you do not want to populate the sourceRoot property.
     *
     * @param - The new value for the sourceRoot property.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setSourceRoot(?string $sourceRoot): this
    {
        $this->sourceRoot = $sourceRoot;
        return $this;
    }

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
    ): this {
        if (!is_callable($fnCallback)) {
            throw new \RuntimeException(
                'The callback function you\'ve passed is not callable.',
                54551300
            );
        }
        $this->userFunctions[$signature] = $fnCallback;
        return $this;
    }

    /**
     * Remove a custom function based on its signature.
     *
     * @param $signature - The identifier of the importer to remove.
     *                      If no importer exists with this identifier the
     *                      list of importers remains unchanged.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function removeFunction(string $signature): this
    {
        unset($this->userFunctions[$signature]);
        return $this;
    }

    /**
     * List all available custom functions.
     *
     * @return - The available custom functions.
     */
    public function listFunctions(): ImmVector<string>
    {
        return new ImmVector(array_keys($this->userFunctions));
    }

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
    ): this {
        if (!is_callable($fnCallback)) {
            throw new \RuntimeException(
                'The callback function you\'ve passed is not callable.',
                54551300
            );
        }
        $this->userImporters[$identifier] = [
            'callback' => $fnCallback,
            'priority' => $priority
        ];
        return $this;
    }

    /**
     * Remove an importer based on its identifier.
     *
     * @param $identifier - The identifier of the importer to remove.
     *                      If no importer exists with this identifier the
     *                      list of importers remains unchanged.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function removeImporter(string $identifier): this
    {
        unset($this->userImporters[$identifier]);
        return $this;
    }

    /**
     * List all available custom importers and their priority.
     * The keys are the identifier, the values their priority.
     *
     * @return - The available importers.
     */
    final public function listImporters(): ImmMap<string, int>
    {
        $list = [];
        foreach ($this->userImporters as $ident => $importer) {
            $list[$ident] = $importer['priority'];
        }
        return new ImmMap($list);
    }

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
    ): this {
        if (isset($this->userImporters[$identifier]['priority'])) {
            $this->userImporters[$identifier]['priority'] = $priority;
        }
        return $this;
    }

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
    ): this {
        $this->headers[$identifier] = [
            'content' => $content,
            'priority' => $priority,
        ];
        return $this;
    }

    /**
     * Remove a header based on its identifier.
     *
     * @param $identifier - The identifier of the header to remove.
     *                      If no header exists with this identifier the list
     *                      of headers remains unchanged.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function removeHeader(string $identifier): this
    {
        unset($this->headers[$identifier]);
        return $this;
    }

    /**
     * List all available headers and their priority.
     * The keys are the identifier, the values their priority.
     *
     * @return - The available headers.
     */
    final public function listHeaders(): ImmMap<string, int>
    {
        $list = [];
        foreach ($this->headers as $ident => $header) {
            $list[$ident] = $header['priority'];
        }
        return new ImmMap($list);
    }

    /**
     * Set the priority of an available header.
     *
     * @param $identifier - The identifier of the header of which
     *                      the priority is to be changed.
     * @param $priority   - The new priority.
     *
     * @return - A shallow copy of the current `Sass` instance.
     */
    final public function setHeaderPriority(string $identifier, int $priority): this
    {
        if (isset($this->headers[$identifier]['priority'])) {
            $this->headers[$identifier]['priority'] = $priority;
        }
        return $this;
    }

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
    <<__Native>>
    final public static function sass2scss(string $sass, int $options = 2): string;

    /**
     * Get the library version of `libsass`.
     *
     * @return - The library version of `libsass`.
     */
    <<__Native>>
    final public static function getLibraryVersion(): string;

    /**
     * Get the version of the sass specification `libsass` implements.
     *
     * @return - The language version that `libsass` implements.
     */
    <<__Native>>
    final public static function getLanguageVersion(): string;

    /**
     * Get the version of the `sass2scss` utility `libsass` ships with.
     *
     * @return - The version of `sass2scss`.
     */
    <<__Native>>
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
    private ?string $string = null; // __toString cache

    protected ?string $sourceFile = null;
    protected ?int $sourceLine = null;
    protected ?int $sourceColumn = null;
    protected ?string $formattedMessage = null;

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
        ?\__SystemLib\Throwable $previous = null
    ) {
        parent::__construct($this->changeMessage($message, true), $code, $previous);
        $this->sourceFile = $sourceFile;
        $this->sourceLine = $sourceLine;
        $this->sourceColumn = $sourceColumn;
        $this->formattedMessage = $this->changeMessage($formattedMessage);
    }

    private function changeMessage(?string $message, bool $clearTrace = false): ?string
    {
        if (empty($message)) {
            return $message;
        }
        // No c functions found in userland :-)
        $message = str_replace('in C function', 'in callback function', $message);

        // Remove the backtrace provided by `libsass` from the error message if requested.
        if ($clearTrace) {
            $marker = "\n\nBacktrace";
            if (($pos = strpos($message, $marker)) > 0) {
                $message = substr($message, 0, $pos);
            }
        }
        return $message;
    }

    /**
     * Get the path to the file that caused the compilation error.
     *
     * @return - The path to the source file.
     */
    public function getSourceFile(): ?string
    {
        return $this->sourceFile;
    }

    /**
     * Get the line in the file that caused the compilation error.
     *
     * @return - The number of the line that caused the error.
     */
    public function getSourceLine(): ?int
    {
        return $this->sourceLine;
    }

    /**
     * Get the column in the file that caused the compilation error.
     *
     * @return - The number of the column that caused the error.
     */
    public function getSourceColumn(): ?int
    {
        return $this->sourceColumn;
    }

    /**
     * Get the complete formatted message that shows the exact position
     * of the compilation error.
     *
     * @return - The complete formatted error message.
     */
    public function getFormattedMessage(): ?string
    {
        return $this->formattedMessage;
    }

    /**
     * Format the exception as a string for display.
     *
     * @return - This exception as a string.
     */
    public function __toString(): string
    {
        if ($this->string === null) {
            $this->string = 'exception \''.get_class($this).'\' with message \''
                .$this->message.'\' in '.$this->file.':'.$this->line."\n";
            if (!is_null($this->sourceFile)) {
                $this->string .= 'Source File: '.$this->sourceFile."\n";
            }
            if (!is_null($this->sourceLine)) {
                $this->string .= 'Source Line: '.$this->sourceLine."\n";
            }
            if (!is_null($this->sourceColumn)) {
                $this->string .= 'Source Column: '.$this->sourceColumn."\n";
            }
            if (!is_null($this->formattedMessage)) {
                $this->string .= "Full message:\n".$this->formattedMessage."\n";
            }
            $this->string .= "Stack trace:\n".$this->getTraceAsString();
        }
        return $this->string;
    }
}

/**
 * Represents a container for a single custom import.
 * It can hold the filename, the scss source string and a map definition.
 */
class SassImport {
    protected string $path = '';
    protected ?string $source = null;
    protected ?string $srcmap  = null;

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
    public function setPath(string $path): this
    {
        $this->path = (string)$path;
        return $this;
    }

    /**
     * Get the path of the file that will be imported.
     *
     * @return - The path of the scss file.
     */
    public function getPath(): string
    {
        return $this->path;
    }

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
    public function setSource(?string $source): this
    {
        $this->source = $source;
        return $this;
    }

    /**
     * Get the scss source that will be imported and compiled.
     *
     * @return - The scss source.
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

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
    public function setSrcMap(?string $map): this
    {
        $this->srcmap = $map;
        return $this;
    }

    /**
     * Get the source map that will be imported and used to re-map
     * the actual sourcemap.
     *
     * NOTE: See the note in the description of `setSrcMap()`.
     *
     * @return - The source map.
     */
    public function getSrcMap(): ?string
    {
        return $this->srcmap;
    }

    final private function finalize(): void
    {
        if (empty($this->path) && !empty($this->source)) {
            // Each entry requires a unique path.
            // See https://github.com/sass/libsass/issues/1292
            $this->path = 'php://temp/'.md5($this->source).'.scss';
        }
    }
}

} // namespace Sass


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
    final public static function cs(mixed $value): SassValue
    {
        if ($value === null) {
            return new SassNull();
        } else if (is_object($value)) {
            if ($value instanceof SassValue) {
                return $value;
            } else if ($value instanceof SassMapPair) {
                return (new SassMap())->add($value);
            }
        } elseif (is_bool($value)) {
            return (new SassBoolean())
                ->setValue($value);
        } elseif (is_int($value) || is_float($value)) {
            return (new SassNumber())
                ->setValue((float)$value);
        } elseif (is_string($value)) {
            return (new SassString())
                ->setValue($value);
        }
        throw new \InvalidArgumentException(
            'Supplied value of type '.gettype($value).' could not converted to a SassValue.',
            54552000
        );
    }
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
    final public function equals(SassValue $value): bool
    {
        return $value instanceof SassNull;
    }

    /**
     * Return a string representation of this `SassNull`.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'null';
    }
}

/**
 * A wrapper for numeric values.
 */
class SassNumber extends SassValue
{
    private num $value = 0;
    private string $unit = '';

    /**
     * Get the value of the number.
     *
     * @return - The value.
     */
    final public function getValue(): num
    {
        return $this->value;
    }

    /**
     * Set the value of the number.
     *
     * @param $unit - The new unit.
     * @param $unit - The new unit (optional).
     *
     * @return - A shallow copy of the current `SassNumber`.
     */
    final public function setValue(num $value, ?string $unit = null): this
    {
        $this->value = $value;
        if (!is_null($unit)) {
            $this->setUnit($unit);
        }
        return $this;
    }

    /**
     * Get the unit of the number.
     *
     * @return - The unit.
     */
    final public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * Set the unit of the number.
     *
     * @param $unit - The new unit.
     *
     * @return - A shallow copy of the current `SassNumber`.
     */
    final public function setUnit(string $unit): this
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * Check if this `SassNumber` equals another `SassNumber`.
     *
     * @param $value - The `SassNumber` to compare with.
     *
     * @return - `true` when it matches, `false` otherwise.
     */
    final public function equals(SassValue $value): bool
    {
        return ($value instanceof SassNumber)
            && ($value->getValue() === $this->getValue())
            && ($value->getUnit() === $this->getUnit());
    }

    /**
     * Return a string representation of this `SassNumber`.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value.$this->unit;
    }
}

/**
 * A wrapper for string values.
 */
class SassString extends SassValue
{
    private string $value = '';
    private bool $isQuoted = false;

    /**
     * Get the wrapped string.
     *
     * @return - The wrapped string.
     */
    final public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set the wrapped string.
     *
     * @param $value - The new string.
     *
     * @return - A shallow copy of the current `SassString`.
     */
    final public function setValue(string $value): this
    {
        $this->value = $value;

        if (!empty($value)
            && ((($quote = substr($value, 0, 1)) === '"') || ($quote === "'"))
            && (substr($value, -1, 1) === $quote)
        ) {
            $this->isQuoted = true;
            $pos = 1;
            $len = strlen($value) - 1;
            while (
                (($pos = strpos($value, $quote, $pos)) !== false)
                && ($pos < $len)
            ) {
                if (substr($value, $pos - 1, 1) !== '\\') {
                    $this->isQuoted = false;
                    break;
                }
            }
        } else {
            $this->isQuoted = false;
        }
        return $this;
    }

    /**
     * Returns whether the string is quoted or not.
     *
     * @return - `true` if the string is quoted,
     *           `false` otherwise.
     */
    final public function isQuoted(): bool
    {
        return $this->isQuoted;
    }

    <<__Native>>
    private static function quoteNative(string $str): string;

    <<__Native>>
    private static function unquoteNative(string $str): string;

    /**
     * Unquote the string.
     *
     * @return - A shallow copy of the current `SassString`.
     */
    public function quote(): this
    {
        if (!$this->isQuoted) {
            $this->value = self::quoteNative($this->value);
            $this->isQuoted = true;
        }
        return $this;
    }

    /**
     * Quote the string.
     *
     * @return - A shallow copy of the current `SassString`.
     */
    public function unquote(): this
    {
        if ($this->isQuoted) {
            $this->value = self::unquoteNative($this->value);
            $this->isQuoted = false;
        }
        return $this;
    }

    /**
     * Check if this string needs quotes (eg. not starting with a letter or
     * containing spaces).
     *
     * @return - `true` when spaces are needed; `false` otherwise.
     */
    public function needsQuotes(): bool
    {
        if (empty($this->value)) {
            return false;
        }
        $str = $this->isQuoted
            ? self::unquoteNative($this->value)
            : $this->value;
        $char = $str[0];
        if (!(
               (($char >= 'a') && ($char <= 'z'))
            || (($char >= 'A') && ($char <= 'Z'))
        )) {
            return true;
        }
        for ($i = 1, $n = strlen($str); $i < $n; ++$i) {
            $char = $str[$i];
            if (!(
                (ord($char) >= 127)
                || (($char >= '0') && ($char <= '9'))
                || (($char >= 'a') && ($char <= 'z'))
                || (($char >= 'A') && ($char <= 'Z'))
                || (($char == '\\') && (($i + 1) <= $n))
            )) {
                return true;
            }
        }
        return false;
    }

    /**
     * Quote the string only if it is necessary.
     *
     * @return - A shallow copy of the current `SassString`.
     */
    public function autoQuote(): this
    {
        if (!$this->isQuoted && $this->needsQuotes()) {
            $this->quote();
        }
        return $this;
    }

    /**
     * Check if this `SassString` equals another `SassString`.
     *
     * @param $value - The `SassString` to compare with.
     *
     * @return - `true` when it matches, `false` otherwise.
     */
    final public function equals(SassValue $value): bool
    {
        return ($value instanceof SassString)
            && ($value->getValue() === $this->getValue());
    }

    /**
     * Return a string representation of this `SassString`.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}

/**
 * A wrapper for boolean values.
 */
class SassBoolean extends SassValue
{
    private bool $value = false;

    /**
     * Get the wrapped boolean.
     *
     * @return - The boolean value of this object.
     */
    final public function getValue(): bool
    {
        return $this->value;
    }

    /**
     * Set the wrapped boolean.
     *
     * @param $value - The new boolean value.
     *
     * @return - A shallow copy of the current `SassBoolean`.
     */
    final public function setValue(bool $value): this
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Check if this `SassBoolean` equals another `SassBoolean`.
     *
     * @param $value - The `SassBoolean` to compare with.
     *
     * @return - `true` when it matches, `false` otherwise.
     */
    final public function equals(SassValue $value): bool
    {
        return ($value instanceof SassBoolean)
            && ($value->getValue() === $this->getValue());
    }

    /**
     * Return a string representation of this `SassBoolean`.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value ? 'true' : 'false';
    }
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
    private int $r = 0;
    private int $g = 0;
    private int $b = 0;
    private float $alpha = 1.0;

    /**
     * Set the alpha channel's value of this color
     *
     * @param $alpha - The value of the alpha channel
     *                 (value in the range of 0 to 1, optional).
     *
     * @return - A shallow copy of the current `SassColor`.
     */
    final public function setAlpha(float $a): this
    {
        $this->alpha = max(min(1.0, $a), 0.0);
        return $this;
    }

    /**
     * Get the alpha channel's value of this color.
     *
     * @return - The value of the alpha channel.
     */
    final public function getAlpha(): float
    {
        return $this->alpha;
    }

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
    ): this {
        $this->r = max(min(255, $r), 0);
        $this->g = max(min(255, $g), 0);
        $this->b = max(min(255, $b), 0);
        if ($alpha !== null) {
            $this->alpha = $alpha;
        }
        return $this;
    }

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
    ): this {
        if (
            !is_array($c)
            || !isset($c['r']) || !is_int($c['r'])
            || !isset($c['g']) || !is_int($c['g'])
            || !isset($c['b']) || !is_int($c['b'])
        ) {
            throw new \InvalidArgumentException(
                'First parameter has to be of type SassColorRGB.',
                54552001
            );
        }
        $this->setRGB($c['r'], $c['g'], $c['b'], $alpha);
        return $this;
    }

    /**
     * Returns the current color as RGB representation.
     *
     * @return - The RGB representation as shape.
     */
    final public function getRGB(): SassColorRGB
    {
        return shape('r' => $this->r, 'g' => $this->g, 'b' => $this->b);
    }

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
    ): this {
        $c = self::hslToRgb($h, $s, $l);
        $this->setRGB($c['r'], $c['g'], $c['b'], $alpha);
        return $this;
    }

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
    ): this {
        if (
            !is_array($c)
            || !isset($c['h']) || !is_float($c['h'])
            || !isset($c['s']) || !is_float($c['s'])
            || !isset($c['l']) || !is_float($c['l'])
        ) {
            throw new \InvalidArgumentException(
                'First parameter has to be of type SassColorHSL.',
                54552002
            );
        }
        $this->setHSL($c['h'], $c['s'], $c['l'], $alpha);
        return $this;
    }

    /**
     * Returns the current color as HSL representation.
     *
     * @return - The HSL representation as shape.
     */
    final public function getHSL(): SassColorHSL
    {
        return self::rgbToHsl($this->r, $this->g, $this->b);
    }

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
    final public static function rgbToHsl(int $r, int $g, int $b): SassColorHSL
    {
        $fr = max(0, min($r, 255)) / 255;
        $fg = max(0, min($g, 255)) / 255;
        $fb = max(0, min($b, 255)) / 255;

        $min = min($fr, $fg, $fb);
        $max = max($fr, $fg, $fb);

        $h = 0.0;
        $s = $max - $min;
        $l = ($max + $min) / 2;

        if ($s != 0) {
            if ($fr == $max) {
                $h = ($fg - $fb) / $s + (($fg < $fb) ? 6 : 0);
            } else if ($fg == $max) {
                $h = ($fb - $fr) / $s + 2;
            } else {
                $h = ($fr - $fg) / $s + 4;
            }
            $s /= ($l < 0.5 ) ? $max + $min : 2 - $max - $min;
            $h *= 60;
        } else {
            $s = (($l > 0) && ($l < 1)) ? 0 : $h;
        }
        return shape('h' => (float)$h, 's' => (float)$s, 'l' => (float)$l);
    }

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
    ): SassColorRGB {
        $hue2rgb = function ($h, $m1, $m2) {
            return 255 * (
                ($h < 60)
                    ? $m1 + ($m2 - $m1) * $h / 60
                    : (($h < 180)
                        ? $m2
                        : (($h < 240)
                            ? $m1 + ($m2 - $m1) * (240 - $h) / 60
                            : $m1
                        )
                    )
                );
        };
        $h = fmod((float)$h, 360) + (($h < 0) ? 360 : 0);
        $m2 = $l + ($l < 0.5 ? $l : 1 - $l) * $s;
        $m1 = 2 * $l - $m2;
        return shape(
            'r' => (int)$hue2rgb(($h >= 240) ? $h - 240 : $h + 120, $m1, $m2),
            'g' => (int)$hue2rgb($h, $m1, $m2),
            'b' => (int)$hue2rgb($h < 120 ? $h + 240 : $h - 120, $m1, $m2)
        );
    }

    /**
     * Check if this `SassColor` equals another `SassColor`.
     *
     * @param $value - The `SassColor` to compare with.
     *
     * @return - `true` when all color channels match, `false`
     *           otherwise.
     */
    final public function equals(SassValue $value): bool
    {
        return ($value instanceof SassColor)
            && ($value->getRGB() === $this->getRGB())
            && ($value->getAlpha() === $this->getAlpha());
    }

    /**
     * Return a string representation of this `SassColor`.
     *
     * @return string
     */
    public function __toString(): string
    {
        if ($this->alpha === 1.0) {
            return 'rgb('.$this->r.', '.$this->g.', '.$this->b.')';
        } else {
            return 'rgba('.$this->r.', '.$this->g.', '.$this->b.', '.
                $this->alpha.')';
        }
    }
}

/**
 * The base interface for all `SassCollection`s.
 */
interface SassCollection<Tk, Te> extends
    //\IndexAccess<SassValue,SassValue>,
    //\HH\Collection<SassMapPair>,
    \HH\KeyedContainer<Tk,SassValue>,
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

    private Vector<SassValue> $list = Vector {};
    private string $separator = self::SEPARATOR_COMMA;

    private bool $recursionDetection = false;

    /**
     * Clone all the elements in this new `SassList` because the original one
     * and this one share the same elements.
     */
    final public function __clone()
    {
        if (!$this->isValid()) {
            throw new \LogicException(
                self::class.' does not support recursion.',
                54552003
            );
        }
        $new = Vector {};
        for ($i = 0, $n = $this->count(); $i < $n; ++$i) {
            $new->add(clone $this->at($i));
        }
        $this->list = $new;
    }

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
    final public function setSeparator(string $separator): this
    {
        if (($separator !== self::SEPARATOR_COMMA)
            && ($separator !== self::SEPARATOR_SPACE)
        ) {
            throw new \InvalidArgumentException(
                'Invalid separator "'.$separator.'". Use '.
                    'SassList::SEPARATOR_COMMA or SassList::SEPARATOR_SPACE.',
                54552004
            );
        }
        $this->separator = $separator;
        return $this;
    }

    /**
     * Gets the currently selected seperator.
     *
     * @return - The separator
     */
    final public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * Returns the first value in the current `SassList`.
     *
     * @return - The first value in the current `SassList`, or `null` if the
     *           `SassList` is empty.
     */
    final public function firstValue(): ?SassValue
    {
        return $this->list->firstValue();
    }

    /**
     * Returns the first key in the current `SassList`.
     *
     * @return - The first key (an integer) in the current `SassList`, or
     *           `null` if the `SassList` is empty.
     */
    final public function firstKey(): ?int
    {
        return $this->list->firstKey();
    }

    /**
     * Returns the last value in the current `SassList`.
     *
     * @return - The last value in the current `SassList`, or `null` if the
     *           current `SassList` is empty.
     */
    final public function lastValue(): ?SassValue
    {
        return $this->list->lastValue();
    }

    /**
     * Returns the last key in the current `SassList`.
     *
     * @return - The last key (an integer) in the current `SassList`, or
     *           `null` if the `SassList` is empty.
     */
    final public function lastKey(): ?int
    {
        return $this->list->lastKey();
    }

    /**
     * Checks if the current `SassList` is empty.
     *
     * @return - `true` if the current `SassList` is empty; `false` otherwise.
     */
    final public function isEmpty(): bool
    {
        return $this->list->isEmpty();
    }

    /**
     * Provides the number of elements in current `SassList`.
     *
     * @return - The number of elements in current `SassList`.
     */
    final public function count(): int
    {
        return $this->list->count();
    }

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
    final public function at(int $k): SassValue
    {
        return $this->list->at($k);
    }

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
    final public function get(int $k): ?SassValue
    {
        return $this->list->get($k);
    }

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
    final public function set(int $k, SassValue $v): this
    {
        $this->list->set($k, $v);
        return $this;
    }

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
    final public function setAll(?KeyedTraversable<int,SassValue> $it): this
    {
        if (!is_null($it)) {
            foreach ($it as $k => $v) {
                $this->set($k, SassValue::cs($v));
            }
        }
        return $this;
    }

    /**
     * Remove all the elements from the current `SassList`.
     *
     * @return - A shallow copy of the current `SassList`.
     */
    final public function clear(): this
    {
        $this->list->clear();
        return $this;
    }

    /**
     * Determines if the specified key is in the current `SassList`.
     *
     * @return - `true` if the specified key is present in the current
     *           `SassList`; returns `false` otherwise.
     */
    final public function containsKey(int $k): bool
    {
        return $this->list->containsKey($k);
    }

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
    final public function add(SassValue $v): this
    {
        $this->list->add($v);
        return $this;
    }

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
    final public function addAll(?Traversable<SassValue> $it): this
    {
        if (!is_null($it)) {
            foreach ($it as $v) {
                $this->add(SassValue::cs($v));
            }
        }
        return $this;
    }

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
    final public function removeKey(int $k): this
    {
        $this->list->removeKey($k);
        return $this;
    }

    /**
     * Remove the last element of the current `SassList` and return it.
     *
     * This function throws an exception if this `SassList` is empty.
     *
     * This `SassList` will have `n - 1` elements after this operation.
     *
     * @return - The value of the last element.
     */
    final public function pop(): SassValue
    {
        return $this->list->pop();
    }

    /**
     * Returns an iterator that points to beginning of the current `SassList`.
     *
     * @return - A `KeyedIterator` that allows you to traverse the current
     *           `SassList`.
     */
    final public function getIterator(): \HH\KeyedIterator<int, SassValue>
    {
        return $this->list->getIterator();
    }

    /**
     * Reverse the elements of the current `SassList` in place.
     *
     * @return - A shallow copy of the current `SassList`.
     */
    final public function reverse(): this
    {
        $this->list->reverse();
        return $this;
    }

    /**
     * Shuffles the values of the current `SassList` randomly in place.
     *
     * @return - A shallow copy of the current `SassList`.
     */
    final public function shuffle(): this
    {
        $this->list->shuffle();
        return $this;
    }

    /**
     * Check if this `SassList` equals another `SassList`.
     *
     * @param $value - The `SassList` to compare with.
     *
     * @return - `true` when all values and their order matches, `false`
     *           otherwise.
     */
    final public function equals(SassValue $value): bool
    {
        if (!($value instanceof SassList)
            || ($value->count() != $this->count())
        ) {
            return false;
        }
        if ($value === $this) {
            return true;
        }
        for ($i = 0, $n = $this->count(); $i < $n; ++$i) {
            if (!$this->at($i)->equals($value->at($i))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if this `SassList` is valid.
     * Valid `SassList`s may not contain recursing elements.
     *
     * @param $seen - A list with all visited elements
     *
     * @return bool
     */
    final public function isValid(Vector<SassValue> $seen = Vector {}): bool
    {
        // avoid recursion
        $seen[] = $this;
        foreach ($this->list as $value) {
            if ($value instanceof SassNeedsValidation) {
                if ($seen->linearSearch($value) >= 0) {
                    return false;
                } else if (!$value->isValid($seen)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Return a string representation of this `SassList`.
     *
     * @return string
     */
    public function __toString(): string
    {
        if ($this->recursionDetection) {
            return '(*SassList RECURSION*)';
        }
        $this->recursionDetection = true;
        $toStr = [];
        foreach ($this->getIterator() as $value) {
            $toStr[] = $value->__toString();
        }
        $this->recursionDetection = false;
        return '('.implode(trim($this->separator).' ', $toStr).')';
    }

    public function __debugInfo(): array
    {
        return ['list' => $this->list, 'separator' => $this->separator];
    }
}

/**
 * `SassMapPair` is a container to hold the key and value for a key-value
 * pair of a `SassMap`. The key and value are of type `SassValue` but
 * `SassMapPair` is not a `SassValue` itsself.
 * `SassMapPair` is not a collection like HHVM's `Pair`.
 */
class SassMapPair
{
    protected ?SassValue $k = null;
    protected ?SassValue $v = null;

    /**
     * Set both the key and value at once. Uses `setKey()` and `setValue()`
     * internally.
     *
     * @param $k - The key
     * @param $v - The value
     *
     * @return - A shallow copy of the current `SassMapPair`.
     */
    final public function set(mixed $k, mixed $v): this
    {
        $this->setKey($k);
        $this->setValue($v);
        return $this;
    }

    /**
     * Get the key of this pair.
     *
     * @return - The key.
     */
    public function getKey(): SassValue
    {
        if ($this->k === null) {
            return new SassNull();
        }
        return $this->k;
    }

    /**
     * Set the key of this `SassMapPair`.
     *
     * @param $k - The key. If it is not a `SassValue`
     *             it will be tried to convert it to one.
     *             If that fails an exception is thrown.
     *
     * @return - A shallow copy of the current `SassMapPair`.
     */
    final public function setKey(mixed $k): this
    {
        if (!is_object($k) || !($k instanceof SassValue)) {
            $k = SassValue::cs($k);
        }
        $this->k = $k;
        return $this;
    }

    /**
     * Get the value of this pair.
     *
     * @return - The key.
     */
    public function getValue(): SassValue
    {
        if ($this->v === null) {
            return new SassNull();
        }
        return $this->v;
    }

    /**
     * Set the value of this `SassMapPair`.
     *
     * @param $k - The value. If it is not a `SassValue`
     *             it will be tried to convert it to one.
     *             If that fails an exception is thrown.
     *
     * @return - A shallow copy of the current `SassMapPair`.
     */
    final public function setValue(mixed $v): this
    {
        if (!is_object($v) || !($v instanceof SassValue)) {
            $v = SassValue::cs($v);
        }
        $this->v = $v;
        return $this;
    }

    /**
     * Creates a `SassMap` out of this key-value pair.
     * The resulting map only contains this pair and
     * has a count of one.
     *
     * @return - A new `SassMap` containing this pair.
     */
    final public function toMap(): SassMap
    {
        if ($this->k === null) {
            $this->k = new SassNull();
        }
        if ($this->v === null) {
            $this->v = new SassNull();
        }
        return (new SassMap())->add($this);
    }
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
    private Map<string, SassMapPair> $map = Map{};

    private bool $recursionDetection = false;

    /**
     * Clone all the elements in this new `SassMap` because the original one
     * and this one share the same elements.
     */
    final public function __clone(): void
    {
        if (!$this->isValid()) {
            throw new \LogicException(
                self::class.' does not support recursion.',
                54552003
            );
        }
        $new = Map {};
        foreach ($this->map->getIterator() as $skey => $pair) {
            $new->set($skey, (new SassMapPair())->set(
                clone $pair->getKey(),
                clone $pair->getValue()
            ));
        }
        $this->map = $new;
    }

    /**
     * Returns a `SassList` containing the values of the current `SassMap`.
     *
     * @return - a `SassList` containing the values of the current `SassMap`.
     */
    final public function values(): SassList
    {
        $values = new SassList();
        foreach ($this->map->getIterator() as $pair) {
            $values->add($pair->getValue());
        }
        return $values;
    }

    /**
     * Returns a `SassList` containing the keys of the current `SassMap`.
     *
     * @return - a `SassList` containing the keys of the current `SassMap`.
     */
    final public function keys(): SassList
    {
        $keys = new SassList();
        foreach ($this->map->getIterator() as $pair) {
            $keys->add($pair->getKey());
        }
        return $keys;
    }

    /**
     * Returns the first value in the current `SassMap`.
     *
     * @return - The first value in the current `SassMap`,
     *           or `null` if the `SassMap` is empty.
     */
    final public function firstValue(): ?SassValue
    {
        return $this->map->firstValue()?->getValue();
    }

    /**
     * Returns the first key in the current `SassMap`.
     *
     * @return - The first key in the current `SassMap`,
     *           or `null` if the `SassMap` is empty.
     */
    final public function firstKey(): ?SassValue
    {
        return $this->map->firstValue()?->getKey();
    }

    /**
     * Returns the last value in the current `SassMap`.
     *
     * @return - The last value in the current `SassMap`,
     *           or `null` if the `SassMap` is empty.
     */
    final public function lastValue(): ?SassValue
    {
        return $this->map->lastValue()?->getValue();
    }

    /**
     * Returns the last key in the current `SassMap`.
     *
     * @return - The last key in the current `SassMap`,
     *           or `null` if the `SassMap` is empty.
     */
    final public function lastKey(): ?SassValue
    {
        return $this->map->lastValue()?->getKey();
    }

    /**
     * Checks if the current `SassMap` is empty.
     *
     * @return - `true` if the current `SassMap` is empty; `false` otherwise.
     */
    final public function isEmpty(): bool
    {
        return $this->map->isEmpty();
    }

    /**
     * Provides the number of elements in the current `SassMap`.
     *
     * @return - The number of elements in the current `SassMap`.
     */
    final public function count(): int
    {
        return $this->map->count();
    }

    //
    // Convert a `SassValue` to a string representation,
    // that can be used as a key for the internal map.
    // Contains the class because a `SassString` 5 looks
    // identical to a `SassNumber` 5.
    //
    // @param $k - The `SassValue` to convert
    //
    // @return - The string result
    //
    private function toStringKey(SassValue $k): string
    {
        return get_class($k).'::'.$k;
    }

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
    final public function at(SassValue $k): SassValue
    {
        try {
            return $this->map->at($this->toStringKey($k))->getValue();
        } catch (\OutOfBoundsException $e) {
            throw new \OutOfBoundsException(
                get_class($k).' key '.$k.' is not defined',
                $e->getCode()
            );
        }
    }

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
    final public function get(SassValue $k): ?SassValue
    {
        return $this->map->get($this->toStringKey($k))?->getValue();
    }

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
    final public function set(SassValue $k, SassValue $v): this
    {
        // The "keys" should not be modifiable by others.
        $k = clone $k;
        $this->map->set(
            $this->toStringKey($k),
            (new SassMapPair())->set($k, $v)
        );
        return $this;
    }

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
    ): this {
        if ($traversable === null) {
            return $this;
        }
        foreach ($traversable as $k => $v) {
            $this->set(SassValue::cs($k), SassValue::cs($v));
        }
        return $this;
    }

    /**
     * Remove all the elements from the current `SassMap`.
     *
     * @return - A shallow copy of the current `SassMap`.
     */
    final public function clear(): this
    {
        $this->map->clear();
        return $this;
    }

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
    final public function containsKey(SassValue $k): bool
    {
        return $this->map->containsKey($this->toStringKey($k));
    }

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
    final public function contains(SassValue $k): bool
    {
        return $this->containsKey($k);
    }

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
    final public function add(SassMapPair $p): this
    {
        $this->set($p->getKey(), $p->getValue());
        return $this;
    }

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
    final public function addAll(?Traversable<SassMapPair> $it): this
    {
        if (($it === null) || empty($it)) {
            return $this;
        }
        foreach ($it as $pair) {
            if (!($pair instanceof SassMapPair)) {
                throw new \InvalidArgumentException(
                    'Expected instance of SassMapPair as element in '
                        .'traversable, got '.(
                            is_object($pair)
                                ? 'instance of '.get_class($pair)
                                : gettype($pair)
                        ),
                    54552004
                );
            }
            $this->add($pair);
        }
        return $this;
    }

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
    final public function removeKey(SassValue $k): this
    {
        $skey = $this->toStringKey($k);
        if ($this->map->containsKey($skey)) {
            $this->map->removeKey($skey);
        }
        return $this;
    }

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
    final public function remove(SassValue $k): this
    {
        return $this->removeKey($k);
    }

    /**
     * Returns an iterator that points to beginning of the current `SassMap`.
     *
     * @return - A `KeyedIterator` that allows you to traverse the current
     *           `SassMap`.
     */
    final public function getIterator(): \HH\KeyedIterator<SassValue,SassValue>
    {
        return new SassMapIterator($this->map);
        /*
        return new class($this->map)
            implements KeyedIterator<SassValue,SassValue>
        {
            private SassMap<string, SassMapPair> $map = Map{};
            private int $position = 0;
            private Vector<string> $klist = Vector{};
            private int $size = 0;

            public f.unction __construct(Map<string, SassMapPair> $map)
            {
                $this->map = $map;

                $this->klist = $this->map->keys();
                $this->size = $this->klist->count();
            }

            public f.unction current(): SassValue
            {
                return $this->map->at(
                    $this->klist[$this->position]
                )->getValue();
            }

            public f.unction key(): SassValue
            {
                return $this->map->at($this->klist[$this->position])->getKey();
            }

            public f.unction valid(): bool
            {
                return $this->position < $this->size;
            }

            public f.unction next(): void
            {
                ++$this->position;
            }

            public f.unction rewind(): void
            {
                $this->position = 0;
            }
        };
        */
    }

    /**
     * Check if this `SassMap` equals another `SassMap`.
     *
     * @param $value - The `SassMap` to compare with.
     *
     * @return - `true` when all keys and values and their order matches,
     *           `false` otherwise.
     */
    final public function equals(SassValue $value): bool
    {
        if (!($value instanceof SassMap)
           || ($value->count() != $this->count())
        ) {
            return false;
        }
        if ($value === $this) {
            return true;
        }

        return $value->keys()->equals($this->keys())
            && $value->values()->equals($this->values());
    }

    /**
     * Check if this `SassMap` is valid.
     * Valid `SassMap`s may not contain recursing elements.
     *
     * @param $seen - A list with all visited elements
     *
     * @return bool
     */
    final public function isValid(Vector<SassValue> $seen = Vector {}): bool
    {
        // avoid recursion
        $seen[] = $this;
        foreach ($this->getIterator() as $key => $value) {
            if ($key instanceof SassNeedsValidation) {
                if ($seen->linearSearch($key) >= 0) {
                    return false;
                } else if (!$key->isValid($seen)) {
                    return false;
                }
            }
            if ($value instanceof SassNeedsValidation) {
                if ($seen->linearSearch($value) >= 0) {
                    return false;
                } else if (!$value->isValid($seen)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Return a string representation of this `SassMap`.
     *
     * @return string
     */
    public function __toString(): string
    {
        if ($this->recursionDetection) {
            return '(*SassMap RECURSION*)';
        }
        $this->recursionDetection = true;
        $strmap = [];
        foreach ($this->map->getIterator() as $skey => $pair) {
            $strmap[] = $pair->getKey().': '.$pair->getValue();
        }
        $this->recursionDetection = false;
        return '('.implode(', ', $strmap).')';
    }

    public function __debugInfo(): array
    {
        return ['map' => $this->map];
    }

}

/**
 * Iterator to traverse a `SassMap`.
 * Will be returned by `SassMap`::`getIterator()`.
 */
final class SassMapIterator implements \HH\KeyedIterator<SassValue,SassValue>
{
    private Map<string, SassMapPair> $map = Map{};
    private int $position = 0;
    private Vector<string> $klist = Vector{};
    private int $size = 0;

    public function __construct(Map<string, SassMapPair> $map)
    {
        $this->map = $map;

        $this->klist = $this->map->keys();
        $this->size = $this->klist->count();
    }

    public function current(): SassValue
    {
        return $this->map->at($this->klist[$this->position])->getValue();
    }

    public function key(): SassValue
    {
        return $this->map->at($this->klist[$this->position])->getKey();
    }

    public function valid(): bool
    {
        return $this->position < $this->size;
    }

    public function next(): void
    {
        ++$this->position;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}

/**
 * Class for generating errors in custom functions in libsass
 */
class SassError extends SassValue
{
    private string $message = '';

    /**
     * Set the new message.
     *
     * @param $message - The new message.
     *
     * @return - A shallow copy of the current `SassError`.
     */
    final public function setMessage(string $message): this
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get the current message
     *
     * @return - The message
     */
    final public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Check if this `SassError` equals another `SassError`.
     *
     * @param $value - The `SassError` to compare with.
     *
     * @return - `true` when it matches, `false` otherwise.
     */
    final public function equals(SassValue $value): bool
    {
        return ($value instanceof SassError)
            && ($value->getMessage() === $this->getMessage());
    }

    /**
     * Convert the error to a string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'SassError: '.$this->message;
    }
}

/**
 * Class for generating warnings in custom functions in libsass
 */
class SassWarning extends SassValue
{
    private string $message = '';

    /**
     * Set the new message.
     *
     * @param $message - The new message.
     *
     * @return - A shallow copy of the current `SassWarning`.
     */
    final public function setMessage(string $message): this
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get the current message
     *
     * @return - The message
     */
    final public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Check if this `SassWarning` equals another `SassWarning`.
     *
     * @param $value - The `SassWarning` to compare with.
     *
     * @return - `true` when it matches, `false` otherwise.
     */
    final public function equals(SassValue $value): bool
    {
        return ($value instanceof SassWarning)
            && ($value->getMessage() === $this->getMessage());
    }

    /**
     * Convert the warning to a string representation.
     *
     * @return string
     */
    public function __toString(): string
    {
        return 'SassWarning: '.$this->message;
    }
}

} // namespace Sass\Types
