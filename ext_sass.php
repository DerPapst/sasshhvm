<?hh

/**
 * HHVM bindings to libsass - Fast, native Sass compiling in HHVM!
 *
 * For a more detailed descriptions of all methods see Sass.hhi
 *
 * https://github.com/derpapst/sasshhvm
 * Based on https://github.com/sensational/sassphp/
 * Copyright (c)2015 Alexander Papst <http://derpapst.org>
 */
class Sass
{
    private array<string> $includePaths = array();
    private int $precision = 5;
    private int $style = self::STYLE_NESTED;
    private int $syntax = self::SYNTAX_SCSS;
    private bool $sourceComments = false;
    private ?string $linefeed = null;
    private ?string $indent = null;
    private bool $embedMap = false;
    private ?string $sourceRoot = null;

    <<__Native>>
    final public function compile(string $source): string;

    <<__Native>>
    final private function compileFileNative(string $fileName): string;

    final public function compileFile(string $fileName): string
    {
        if (empty($fileName)) {
            throw new SassException(
                'The file name may not be empty.', 1435750241
            );
        }
        // Make the file path absolute
        if (substr($fileName, 0, 1) !== '/') {
            $fileName = getcwd().'/'.$fileName;
        }
        if (!file_exists($fileName) || !is_readable($fileName)) {
            throw new SassException(
                'The file can not be read.', 1435750470
            );
        }
        return $this->compileFileNative($fileName);
    }

    <<__Native>>
    final private function compileFileWithMapNative(string $fileName, string $mapFileName): array;

    final public function compileFileWithMap(string $fileName, ?string $mapFileName = null): SassResonse
    {
        if (empty($fileName)) {
            throw new SassException(
                'The file name may not be empty.', 1435750241
            );
        }
        // Make  the file path absolute
        if (substr($fileName, 0, 1) !== '/') {
            $fileName = getcwd().'/'.$fileName;
        }
        if (!file_exists($fileName) || !is_readable($fileName)) {
            throw new SassException(
                'The file can not be read.', 1435750470
            );
        }
        $mapFileName = empty($mapFileName) ? $fileName.'.map' : $mapFileName;

        $response = $this->compileFileWithMapNative($fileName, $mapFileName);

        return shape('css' => $response['css'], 'map' => $response['map']);
    }

    public function getStyle(): int
    {
        return $this->style;
    }

    final public function setStyle(int $style): this
    {
        if (!in_array($style, array (
            self::STYLE_NESTED, self::STYLE_EXPANDED,
            self::STYLE_COMPACT, self::STYLE_COMPRESSED
        ))) {
            throw new \InvalidArgumentException(
                'This style is not supported.', 1435749818
            );
        }
        $this->style = $style;
        return $this;
    }

    public function getSyntax()
    {
        return $this->syntax;
    }

    final public function setSyntax(int $syntax): this
    {
        if (!in_array($syntax, array (
            self::SYNTAX_SCSS, self::SYNTAX_SASS
        ))) {
            throw new \InvalidArgumentException(
                'This syntax is not supported.', 1447954833
            );
        }
        $this->syntax = $syntax;
        return $this;
    }

    public function getIncludePaths(): array<string>
    {
        return $this->includePaths;
    }

    final public function addIncludePath(string $includePath): this
    {
        // Make the file path absolute
        if (substr($includePath, 0, 1) !== '/') {
            $includePath = getcwd().'/'.$includePath;
        }
        if (!is_dir($includePath) || !is_readable($includePath)) {
            throw new \RuntimeException(
                'The path '.$includePath.' does not exist or is not readable',
                1435748077
            );
        }
        $this->includePaths[] = $includePath;
        return $this;
    }

    final public function setIncludePaths(array<string> $includePaths): this
    {
        $this->includePaths = array();
        foreach ($includePaths as $idx => $includePath) {
            $this->addIncludePath($includePath);
        }
        return $this;
    }

    public function getPrecision(): int
    {
        return $this->precision;
    }

    final public function setPrecision(int $precision): this
    {
        if ($precision < 0) {
            throw new SassException(
                'The precision has to be greater or equal than 0.', 1435750706
            );
        }
        $this->precision = $precision;
        return $this;
    }

    public function getIncludesSourceComments(): bool
    {
        return $this->includesSourceComments;
    }

    final public function setIncludesSourceComments(bool $sourceComments): this
    {
        $this->sourceComments = $sourceComments;
        return $this;
    }

    public function includesSourceComments(): bool
    {
        return $this->getIncludesSourceComments();
    }

    final public function includeSourceComments(bool $sourceComments): this
    {
        return $this->setIncludesSourceComments($sourceComments);
    }

    public function getLinefeed(): ?string
    {
        return $this->linefeed;
    }

    final public function setLinefeed(?string $linefeed): this
    {
        $this->linefeed = $linefeed;
        return $this;
    }

    public function getIndent(): ?string
    {
        return $this->indent;
    }

    final public function setIndent(?string $indent): this
    {
        $this->indent = $indent;
        return $this;
    }

    public function getEmbedMap(): bool
    {
        return $this->embedMap;
    }

    final public function setEmbedMap(bool $embedMap): this
    {
        $this->embedMap = $embedMap;
        return $this;
    }

    public function isMapEmbedded(): bool
    {
        return $this->getEmbedMap();
    }

    final public function embedMap(bool $embedMap): this
    {
        return $this->setEmbedMap($embedMap);
    }

    public function getSourceRoot(): ?string
    {
        return $this->sourceRoot;
    }

    final public function setSourceRoot(?string $sourceRoot): this
    {
        $this->sourceRoot = $sourceRoot;
        return $this;
    }

    <<__Native>>
    final public static function getLibraryVersion(): string;
}

class SassException extends Exception
{
}

type SassResonse = shape('css' => string, 'map' => string);
