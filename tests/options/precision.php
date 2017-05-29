<?hh

use Sass\Sass;

function testPrecision(): void
{
    $src = '.selector { width: (5/7) * 1%; }';
    $sass = new Sass();
    try {
        var_dump($sass->getPrecision());
        echo $sass->compile($src)."\n\n";
        $sass->setPrecision(3);
        var_dump($sass->getPrecision());
        echo $sass->compile($src)."\n\n";
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }

    try {
        $sass->setPrecision(-1);
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
}

testPrecision();
