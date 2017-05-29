<?hh

namespace Sass;

use Sass\Types\SassValue;

function testCIEngineException(): void
{
    try {
        $res = (new Sass())
            ->addImporter('errorimporter', function (string $curPath, string $prevPath): ?Traversable<?SassImport> {
                // UNSAFE
                idonotexist();
                return null;
            })
            ->compile('
                @import "stuff";
            ');
        echo $res."\n\n";
    } catch (\Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous: '.$prev."\n\n";
        }
    } catch (\Error $e) {
        echo 'Caught '.$e."\n\n";
    }
}

testCIEngineException();
