<?hh

use Sass\Sass;
use Sass\Types\SassValue;

function testException(): void
{
    try {
        $res = (new Sass())
            ->addFunction('derp()', function (ImmVector<SassValue> $args): SassValue {
                throw new LogicException('A random exception appears...', 1471385790);
                return SassValue::cs(null);
            })
            ->compile('
                $herp: derp();
            ');
        echo $res."\n\n";
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
}

if (count(get_included_files()) === 1) {
    testException();
}
