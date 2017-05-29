<?hh

use Sass\Sass;
use Sass\Types\SassValue;

function testCFEngineException(): void
{
    try {
        $res = (new Sass())
            ->addFunction('derp()', function (ImmVector<SassValue> $args): SassValue {
                // UNSAFE
                idonotexist();
                return SassValue::cs(null);
            })
            ->compile('
                $herp: derp();
            ');
        echo $res."\n\n";
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous: '.$prev."\n\n";
        }
    } catch (Error $e) {
        echo 'Caught '.$e."\n\n";
    }
}

if (count(get_included_files()) === 1) {
    testCFEngineException();
}
