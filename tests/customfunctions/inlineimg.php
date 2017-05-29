<?hh

use Sass\Sass;
use Sass\Types\SassValue;
use Sass\Types\SassNumber;
use Sass\Types\SassString;
use Sass\Types\SassList;

function demoInlineImage(): void
{
    try {
        $res = (new Sass())
            ->setStyle(Sass::STYLE_EXPANDED)
            ->setIndent("    ")
            ->addFunction('inline-image($url)', function (ImmVector<SassValue> $args): SassValue {
                invariant($args->count() === 1, 'There is exactly one argument.');
                $url = $args[0];
                invariant($url instanceof SassString, 'First argument is of type SassString.');

                $url->unquote();
                if (!is_file($url.'') || !is_readable($url.'')) {
                    throw new \RuntimeException('The image '.$url.' is not readable.', 1471257015);
                }
                $img = base64_encode(file_get_contents($url.''));
                return SassValue::cs('url("data:image/png;base64,'.$img.'")');
            })
            ->addFunction('image-size($url, $factor:1)', function (ImmVector<SassValue> $args): SassValue {
                invariant($args->count() === 2, 'There is exactly two arguments.');
                $url = $args[0];
                $factor = $args[1];
                invariant($url instanceof SassString, 'First argument is of type SassString.');
                invariant($factor instanceof SassNumber, 'Second argument is of type SassNumber.');

                $url->unquote();
                if (!is_file($url.'') || !is_readable($url.'')) {
                    throw new \RuntimeException('The image '.$url.' is not readable.', 1471257015);
                }
                $size = getimagesize($url.'');
                $size[0] *= $factor->getValue();
                $size[1] *= $factor->getValue();
                return (new SassList())
                    ->add((new SassNumber())->setValue($size[0], 'px'))
                    ->add((new SassNumber())->setValue($size[1], 'px'))
                    ->setSeparator(SassList::SEPARATOR_SPACE);
            })
            ->compile('
                body {
                    background-image: inline-image("tests/resources/img/bg.png");
                    background-size: image-size("tests/resources/img/bg.png", 0.5);
                }
            ');
        echo $res."\n\n";
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
}

demoInlineImage();
