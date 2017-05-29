<?hh

use Sass\Sass;
use Sass\SassImport;

function testMultipleImporters(): void
{
    $sass = new Sass();
    $sass->setStyle(Sass::STYLE_EXPANDED)->setIndent("    ");
    $sass->addIncludePath('tests/resources/');

    $sass->addImporter('http', function (string $curPath, string $prevPath): ?Traversable<?SassImport> {
        echo "== Importer 'http' ==\n";
        echo "    args\n        \$curPath:  ".$curPath."\n        \$prevPath: ".$prevPath."\n";
        // here the scss could be fetched with eg. curl
        if (preg_match('/^https?:\/\//', $curPath)) {
            return [
                (new SassImport())
                    ->setPath($curPath)
                    ->setSource('.remote { border: 1px solid #000; }')
            ];
        }
        // let another importer or libsass handle the import.
        return null;
    }, 2);

    $sass->addImporter('local', function (string $curPath, string $prevPath): ?Traversable<?SassImport> {
        echo "== Importer 'local' ==\n";
        echo "    args\n        \$curPath:  ".$curPath."\n        \$prevPath: ".$prevPath."\n";
        if ($curPath === 'local.scss') {
            return Vector {
                (new SassImport())->setSource('.local { border: 1px solid #f00; }'),
                (new SassImport())->setPath('scss/more/links.scss'),
            };
        }
        // let another importer or libsass handle the import.
        return null;
    }, 1);

    try {
        $r = $sass->compileWithMap('
            @import "http://example.org/scss/remote.scss";
            @import "scss/herpderp.scss";
            @import "local.scss";
        ', 'test.map');
        print_r($r);
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
}

if (count(get_included_files()) === 1) {
    testMultipleImporters();
}
