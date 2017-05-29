<?hh

use Sass\Sass;

function testCompileFileWithMap(): void
{
    $sass = new Sass();
    try {
        var_dump($sass->compileFileWithMap('tests/resources/scss/import.scss'));
        var_dump($sass->getSourceRoot());
        $sass->setSourceRoot('/herp/derp');
        var_dump($sass->getSourceRoot());
        var_dump($sass->compileFileWithMap('tests/resources/scss/import.scss')['map']);
    } catch (Exception $e) {
        echo 'Caught '.$e."\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n";
        }
    }
    echo "\n";
}

testCompileFileWithMap();
