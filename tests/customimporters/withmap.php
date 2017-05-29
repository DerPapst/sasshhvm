<?hh

namespace Sass;

function testWithMap(): void
{
    $sass = new Sass();
    $sass->setStyle(Sass::STYLE_EXPANDED)->setIndent("    ");
    $sass->includeSourceComments(true);

    $sass->addImporter('withmap', function (string $curPath, string $prevPath): ?Traversable<?SassImport> {
        return [
            (new SassImport())
                ->setPath($curPath)
                ->setSource('a { color: red; }')
                ->setSrcMap(json_encode([
                    'version' => 3,
                    'sources' => [$curPath.'.db'],
                    // | Passed to libsass but currently not used.
                    // | See: https://github.com/sass/libsass/blob/master/docs/api-importer.md
                    // V      about `Return Imports`.
                    'mappings' => ';AAAA,CAAC,CAAC;EAAE,KAAK,EAAE,GAAI,GAAI',
                ]))
        ];
    });

    try {
        $res = $sass->compileWithMap('@import "herpderp";', 'herpderp.map');
        print_r($res);
        echo "\n\n";
    } catch (\Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
}

testWithMap();
