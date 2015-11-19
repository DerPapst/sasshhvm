<?hh
$sass = new Sass();
$sass->setSyntax(Sass::SYNTAX_SASS);
try {
	var_dump($sass->compileFile('tests/sass/nested.sass'));
} catch (Exception $se) {
	echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
}
