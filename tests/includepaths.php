<?hh
$sass = new Sass();
try {
	$sass->setIncludePaths(array());
} catch (Exception $se) {
	echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
}
try {
	$sass->setIncludePaths(array(__DIR__, 'tests/sass', 'i/dont/exist'));
} catch (Exception $se) {
	echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
}
try {
	$sass->addIncludePath('tests');
} catch (Exception $se) {
	echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
}
try {
	$sass->addIncludePath('the/final/frontier');
} catch (Exception $se) {
	echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
}
