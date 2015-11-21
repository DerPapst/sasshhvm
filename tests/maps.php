<?hh

function testEmbedMap(?string $sourceRoot = null): void
{
    $sass = new Sass();
    $sass->setSourceRoot($sourceRoot);
    $sass->embedMap(true);
    try {
        $r = $sass->compileFile('tests/sass/import.scss');
        var_dump($r);
        $m = [];
        if (preg_match('/json;base64,([a-zA-Z0-9=]+)/s', $r, $m)) {
            echo "Embedded map: "; var_dump(base64_decode($m[1]));
        } else {
            echo "Embedded map not found.\n";
        }
    } catch (Exception $se) {
        echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
    }
}

function testCompileFileWithMap()
{
    $sass = new Sass();
    try {
        var_dump($sass->compileFileWithMap('tests/sass/import.scss'));
    } catch (Exception $se) {
        echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
    }
}

testEmbedMap();
testEmbedMap('/herp/derp');
testCompileFileWithMap();
