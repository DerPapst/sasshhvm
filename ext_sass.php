<?hh

/**
 * Sass
 * HHVM bindings to libsass - fast, native Sass parsing in HHVM!
 *
 * https://github.com/derpapst/sasshhvm
 * Based on https://github.com/sensational/sassphp/
 * Copyright (c)2015 Alexander Papst <http://derpapst.org>
 */
class Sass {
	/*
	const STYLE_NESTED = <int>;
	const STYLE_EXPANDED = <int>;
	const STYLE_COMPACT = <int>;
	const STYLE_COMPRESSED = <int>;

	const SYNTAX_SCSS = <int>;
	const SYNTAX_SASS = <int>;
	*/

	private array<string> $includePaths = array();
	private int $precision = 5;
	private int $style = self::STYLE_NESTED;
	private int $syntax = self::SYNTAX_SCSS;
	private bool $sourceComments = false;

	/**
	 * Parse a string of Sass; a basic input -> output affair.
	 * @param string $source - String containing some sass source code.
	 * @throws SassException - If the source is invalid.
	 * @return string - Compiled css code
	 */
	<<__Native>>
	public function compile(string $source): string;

	/**
	 * The native implementation of compileFile().
	 */
	<<__Native>>
	final private function compileFileNative(string $fileName): string;

	/**
	 * Parse a whole file full of Sass and return the css output.
	 * Only local files without the use of a stream or wrapper are supported.
	 * @param string $fileName
	 *    String containing the file name of a sass source code file.
	 * @throws SassException
	 *    If the file can not be read or source is invalid.
	 * @return string - Compiled css code
	 */
	final public function compileFile(string $fileName): string {
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
		return $this->compileFileNative($fileName);
	}

	/**
	 * Alias of self::compileFile()
	 * @param string $fileName
	 *    String containing the file name of a sass source code file.
	 * @return string - Compiled css code
	 */
	final public function compile_file(string $file_name): string {
		return $this->compileFile($file_name);
	}

	/**
	 * Get the currently used formatting style. Default is Sass::STYLE_NESTED.
	 * @return int
	 */
	public function getStyle(): int {
		return $this->style;
	}

	/**
	 * Set the formatting style.
	 * Available styles are:
	 *  * Sass::STYLE_NESTED
	 *  * Sass::STYLE_EXPANDED
	 *  * Sass::STYLE_COMPACT
	 *  * Sass::STYLE_COMPRESSED
	 * @param int $style
	 * @throws \InvalidArgumentException - If the style is not supported.
	 * @return Sass
	 */
	final public function setStyle(int $style): Sass {
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

	/**
	 * Get the currently used syntax type. Default is Sass::SYNTAX_SCSS.
	 * @return int
	 */
	public function getSyntax() {
		return $this->syntax;
	}

	/**
	 * Set the syntax type for the input files/strings.
	 * Available syntaxes are:
	 *   * Sass::SYNTAX_SCSS
	 *   * Sass::SYNTAX_SASS
	 * @param int $syntax
	 * @throws \InvalidArgumentException - If the syntax is not supported.
	 * @return Sass
	 */
	final public function setSyntax(int $syntax): Sass {
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

	/**
	 * Gets the currently used include paths where the compiler will search for
	 * included files.
	 * @return array
	 */
	public function getIncludePaths(): array<string> {
		return $this->includePaths;
	}

	/**
	 * Add a path for searching for included files.
	 * Only local directories without the use of a stream or wrapper
	 * are supported.
	 * @param string $includePath - The path to look for further sass files.
	 * @throws SassException - If the path does not exist or is not readable.
	 * @return Sass
	 */
	final public function addIncludePath(string $includePath): Sass {
		// Make  the file path absolute
		if (substr($includePath, 0, 1) !== '/') {
			$includePath = getcwd().'/'.$includePath;
		}
		if (!is_dir($includePath) || !is_readable($includePath)) {
			throw new SassException(
				'The path '.$includePath.' does not exist or is not readable',
				1435748077
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
	 * @param array<string> $includePaths
	 *     The paths to look for further sass files.
	 * @throws SassException - If one path does not exist or is not readable.
	 * @return Sass
	 */
	final public function setIncludePaths(array<string> $includePaths): Sass {
		$this->includePaths = array();
		foreach ($includePaths as $idx => $includePath) {
			$this->addIncludePath($includePath);
		}
		return $this;
	}

	/**
	 * Get the currently used precision for decimal numbers.
	 * @return int
	 */
	public function getPrecision(): int {
		return $this->precision;
	}

	/**
	 * Set the precision that will be used for decimal numbers.
	 * @param int $precision
	 * @return Sass
	 */
	final public function setPrecision(int $precision): Sass {
		if ($precision < 0) {
			throw new SassException(
				'The precision has to be greater or equal than 0.', 1435750706
			);
		}
		$this->precision = $precision;
		return $this;
	}

	/**
	 * Returns whether the compiled css files contain comments
	 * indicating the corresponding source line.
	 * @return bool
	 */
	public function includesSourceComments(): bool {
		return $this->includesSourceComments;
	}

	/**
	 * Pass true to enable emitting comments in the generated CSS indicating
	 * the corresponding source line.
	 * @param bool $sourceComments
	 * @return Sass
	 */
	public function includeSourceComments(bool $sourceComments): Sass {
		$this->sourceComments = $sourceComments;
		return $this;
	}

	/**
	 * Get the library version of libsass.
	 * @return string
	 */
	<<__Native>>
	final public static function getLibraryVersion(): string;
}

/**
 * Exception for Sass.
 */
class SassException extends Exception { }
