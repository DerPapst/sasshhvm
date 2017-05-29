<?hh

use Sass\Sass;
use Sass\Types\SassValue;
use Sass\Types\SassString;

class SassFakeValue extends SassValue {
    public function equals(SassValue $value): bool { return true; }
    public function __toString(): string { return __CLASS__; }
}

function runFunction(Sass $sass, mixed $returnValue): void
{
    $sass->addFunction('func()', function (ImmVector<SassValue> $args): SassValue use ($returnValue) {
        echo "== Function called ==\n";
        echo "Value to be returned:\n";
        ob_start();
        var_dump($returnValue);
        echo '    '.str_replace("\n", "\n    ", trim(ob_get_clean()))."\n\n";
        // UNSAFE
        return $returnValue;
    });

    try {
        echo $sass->compile('.test { width: 5px; content: func(); }');
    } catch (Exception $e) {
        echo 'Caught '.$e;
        if (($prev = $e->getPrevious()) != null) {
            echo "\nPrevious Exception: ".$prev;
        }
    }
    echo "\n\n\n";
}

function testCFReturnValues(): void
{
    $sass = new Sass();
    $sass->setStyle(Sass::STYLE_EXPANDED)->setIndent("    ");

    foreach ([
        5, [5], 'string', null, true, new stdClass(),
        new SassFakeValue(),
        (new SassString())->setValue('sassvalue')
    ] as $returnValue) {
        runFunction($sass, $returnValue);
    }
}

testCFReturnValues();
