<?hh

use Sass\Sass;
use Sass\SassImport;

function runImporter(Sass $sass, mixed $returnValue): void
{
    $sass->addImporter('importer', function (string $curPath, string $prevPath): ?Traversable<?SassImport> use ($returnValue) {
        echo "== Importer Called ==\n";
        echo "Value to be returned:\n";
        ob_start();
        var_dump($returnValue);
        echo '    '.str_replace("\n", "\n    ", trim(ob_get_clean()))."\n\n";
        // UNSAFE
        return $returnValue;
    });

    try {
        $res = $sass->compileWithMap('@import "herpderp";', 'herpderp.map');
        print_r($res);
    } catch (Exception $e) {
        echo 'Caught '.$e;
        if (($prev = $e->getPrevious()) != null) {
            echo "\nPrevious Exception: ".$prev;
        }
    }
    echo "\n\n\n";
}

function testCIReturnValues(): void
{
    $sass = new Sass();
    $sass->setStyle(Sass::STYLE_EXPANDED)->setIndent("    ");
    $sass->includeSourceComments(true);

    foreach ([
        5, [5], '.foo {width: 5px};', null, true, new stdClass(),
        [], [null],
        (new SassImport())->setSource('.foo {width: 5px};'),
        [
            (new SassImport())->setSource('.import-1 { border: 1px solid #f0f; }'),
            null,
            (new SassImport())->setPath('tests/resources/scss/herpderp.scss')
        ],
        Vector{(new SassImport())->setSource('.import-vector { background: #ff0; }')}
    ] as $returnValue) {
        runImporter($sass, $returnValue);
    }
}

testCIReturnValues();
