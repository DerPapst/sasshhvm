<?hh

use Sass\Sass;
use Sass\Types\SassValue;
use Sass\Types\SassString;

function myfunc(ImmVector<SassValue> $args): SassValue {
    return (new SassString())->setValue(__FUNCTION__.' called')->autoQuote();
}

class MyClass {
    public static function staticMethod(ImmVector<SassValue> $args): SassValue {
        return (new SassString())->setValue(__METHOD__.' called')->autoQuote();
    }

    public function instanceMethod(ImmVector<SassValue> $args): SassValue {
        return (new SassString())->setValue(__METHOD__.' called')->autoQuote();
    }

}

function testCallbackTypes(): void {
    $sass = new Sass();
    $sass->setStyle(Sass::STYLE_EXPANDED)->setIndent("    ");

    $closure = function (ImmVector<SassValue> $args): SassValue {
        return (new SassString())->setValue(__FUNCTION__.' called')->autoQuote();
    };

    $function = fun('myfunc');
    $instanceMethod = inst_meth(new MyClass(), 'instanceMethod');
    $staticMethod   = class_meth(MyClass::class, 'staticMethod');

    $sass->addFunction('closure()', $closure);
    $sass->addFunction('function()',  $function);
    $sass->addFunction('instanceMethod()', $instanceMethod);
    $sass->addFunction('staticMethod()',   $staticMethod);

    try {
        $css = $sass->compile('
            .test {
                test-closure: closure();
                test-function: function();
                test-instance-method: instanceMethod();
                test-static-method: staticMethod();
            }
        ');
        echo $css."\n\n";
    } catch (\Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
}

testCallbackTypes();
