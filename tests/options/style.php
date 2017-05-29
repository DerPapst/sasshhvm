<?hh

use Sass\Sass;

function testStyle(): void
{
    $sass = new Sass();
    var_dump($sass->getStyle() === Sass::STYLE_NESTED);

    try {
        $sass->setStyle(Sass::STYLE_COMPRESSED);
        var_dump($sass->getStyle() === Sass::STYLE_COMPRESSED);
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }

    try {
        $sass->setStyle(9001);
    } catch (Exception $e) {
        echo 'Caught '.$e."\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n";
        }
    }

    $oClass = new ReflectionClass(Sass::class);
    $styles = [];
    foreach ($oClass->getConstants() as $c => $v) {
        if (strpos($c, 'STYLE_') !== false) {
            assert(is_int($v));
            $styles[$c] = (int)$v;
        }
    }
    ksort($styles);
    print_r(array_keys($styles));

    foreach ($styles as $c => $v) {
        try {
            $sass->setStyle($v);
            echo '== '.$c." ==\n"
                .$sass->compile(file_get_contents('tests/resources/scss/more/links.scss'))
                ."\n";
        } catch (Exception $e) {
            echo 'Caught '.$e."\n";
            if (($prev = $e->getPrevious()) != null) {
                echo 'Previous Exception: '.$prev."\n";
            }
        }
    }
}

testStyle();
