<?hh

use Sass\Sass;

function testCompileWithMap(): void
{
    $sass = new Sass();
    $sass->addIncludePath('tests/resources/scss');
    try {
        var_dump($sass->compileWithMap('@import "nested.scss"; @import "more/links";', 'test.map'));
    } catch (Exception $e) {
        echo 'Caught '.$e."\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n";
        }
    }
    echo "\n";
}

testCompileWithMap();
