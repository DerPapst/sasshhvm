<?hh

use Sass\Sass;

function testSourceComments(): void
{
    $sass = new Sass();
    var_dump($sass->includesSourceComments());

    $sass->includeSourceComments(true);
    var_dump($sass->includesSourceComments());

    try {
        echo $sass->compileFile('tests/resources/scss/import.scss');
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
}

testSourceComments();
