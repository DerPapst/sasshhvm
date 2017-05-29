<?hh

namespace Sass;

use Sass\Types\SassValue;
use Sass\Types\SassNull;

function testFunctionHandling(): void
{
    $sass = new Sass();

    var_dump($sass->listFunctions());

    $sass->addFunction('foo()', function (ImmVector<SassValue> $args): SassValue {
        return new SassNull();
    });
    $sass->addFunction('bar($a)', function (ImmVector<SassValue> $args): SassValue {
        return new SassNull();
    });
    $sass->addFunction('herp($a, $b)', function (ImmVector<SassValue> $args): SassValue {
        return new SassNull();
    });

    var_dump($sass->listFunctions());

    $sass->removeFunction('bar($a)');
    $sass->removeFunction('idontexist()');

    var_dump($sass->listFunctions());
}

testFunctionHandling();
