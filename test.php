<?php

if (!extension_loaded("sass") || !class_exists("Sass", false)) {
	echo "The SASS extension could not be loaded.\n";
	exit(-1);
}

foreach (glob('tests/*.php') as $file) {
	echo '>> '.$file."\n";
	require_once($file);
	echo "\n";
}
