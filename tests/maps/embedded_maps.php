<?hh

use Sass\Sass;

function testEmbedMap(): void
{
    $sass = new Sass();

    var_dump($sass->isMapEmbedded());
    $sass->embedMap(true);
    var_dump($sass->getEmbedMap());

    try {
        $r = $sass->compileFile('tests/resources/scss/import.scss');
        echo $r."\n\n";
        $m = [];
        if (preg_match('/json;base64,([a-zA-Z0-9=]+)/s', $r, $m)) {
            echo "Embedded map: "; var_dump(base64_decode($m[1]));
        } else {
            echo "Embedded map not found.\n";
        }
    } catch (Exception $e) {
        echo 'Caught '.$e."\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n";
        }
    }
}

testEmbedMap();
