<?hh
$sass = new Sass();
$sass->setStyle(Sass::STYLE_EXPANDED);
try {
	var_dump($sass->compile(file_get_contents('tests/sass/more/links.scss')));
} catch (Exception $se) {
	echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
}
