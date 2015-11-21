<?hh

function testOptionsStyle(): void
{
    $sass = new Sass();
    try {
        $sass->setStyle(9001);
    } catch (Exception $se) {
        echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
    }
    try {
        $sass->setStyle(Sass::STYLE_COMPRESSED);
        var_dump($sass->getStyle() === Sass::STYLE_COMPRESSED);
    } catch (Exception $se) {
        echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
    }
}

function testOptionsPrecision(): void
{
    $sass = new Sass();
    try {
        $sass->setPrecision(-1);
    } catch (Exception $se) {
        echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
    }
    try {
        $sass->setPrecision(3);
        var_dump($sass->getPrecision() === 3);
    } catch (Exception $se) {
        echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
    }
}

function testOptionsSyntax(): void
{
    $sass = new Sass();
    try {
        $sass->setSyntax(9001);
    } catch (Exception $se) {
        echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
    }
    try {
        $sass->setSyntax(Sass::SYNTAX_SASS);
        var_dump($sass->getSyntax() === Sass::SYNTAX_SASS);
    } catch (Exception $se) {
        echo 'Caught '.get_class($se)." in ".$se->getFile()." on line ".$se->getLine()."\nMessage: ".$se->getMessage()."\n".$se->getTraceAsString()."\n";
    }
}

testOptionsStyle();
testOptionsPrecision();
testOptionsSyntax();
