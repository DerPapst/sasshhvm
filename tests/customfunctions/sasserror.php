<?hh

use Sass\Sass;
use Sass\Types\SassValue;
use Sass\Types\SassError;
use Sass\Types\SassWarning;

function testSassError(): void
{
    $sass = (new Sass())
        ->setStyle(Sass::STYLE_EXPANDED)
        ->setIndent("    ")
        ->addFunction('make-warning()', function (ImmVector<SassValue> $args): SassValue {
            return (new SassWarning())->setMessage('I am a warning');
        })
        ->addFunction('make-error()', function (ImmVector<SassValue> $args): SassValue {
            return (new SassError())->setMessage('I am an error');
        });
    try {
        $css = $sass->compile('
            body {
                color: make-warning();
            }
        ');
        echo $css."\n\n";
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }

    try {
        $css = $sass->compile('
            body {
                color: make-error();
            }
        ');
        echo $css."\n\n";
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
}

testSassError();
