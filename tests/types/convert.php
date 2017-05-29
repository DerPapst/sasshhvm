<?hh

namespace Sass\Types;

function testSassValueConvert(): void
{
    var_dump(SassValue::cs(null));
    var_dump(SassValue::cs(true));
    var_dump(SassValue::cs(5));
    var_dump(SassValue::cs('string'));
    var_dump(SassValue::cs(
        (new SassList())->add(
            SassValue::cs((new SassMapPair())->set('k', 'v'))
        )
    ));
    try {
        var_dump(SassValue::cs(new \stdClass()));
    } catch (\Exception $e) {
        echo 'caught '.$e;
    }
    echo "\n";
}

testSassValueConvert();
