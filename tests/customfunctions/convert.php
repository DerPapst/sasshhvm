<?hh

namespace Sass;

use Sass\Types\SassValue;
use Sass\Types\SassNull;
use Sass\Types\SassNumber;
use Sass\Types\SassString;
use Sass\Types\SassBoolean;
use Sass\Types\SassColor;
use Sass\Types\SassCollection;
use Sass\Types\SassList;
use Sass\Types\SassMapPair;
use Sass\Types\SassMap;

function testConvert(): void
{
    $sass = (new Sass())
        ->setStyle(Sass::STYLE_EXPANDED)
        ->setIndent('    ')
        ->addFunction('convert($arg)', function (ImmVector<SassValue> $args): SassValue {
            invariant($args->count() === 1, 'There should be only one element in the provided arguments vector.');
            $value = $args[0];
            echo 'convert('.get_class($value)."):\n";
            echo '    '.$value."\n";
            return $value;
        })
    ;

    try {
        $css = $sass->compile('
            .sass-null {
                content: inspect(convert(null));
            }
            .sass-number-5px {
                content: inspect(convert(5px));
            }
            .sass-number-35-5p {
                content: inspect(convert(35.5%));
            }
            .sass-string-unquot {
                content: inspect(convert(herpderp));
            }
            .sass-string-quot {
                content: inspect(convert("herp derp"));
            }
            .sass-boolean {
                content: inspect(convert(true));
            }
            .sass-color-rgba {
                content: inspect(convert(rgba(231, 45, 189, 0.9)));
            }
            .sass-color-hsl {
                content: inspect(convert(hsl(270, 100%, 50%)));
            }
            .sass-list {
                content: inspect(convert((false, #345, "foo")));
            }
            .sass-map {
                content: inspect(convert((false: "false", null: "null", (): "list")));
            }
        ');
        echo "\n".$css;
    } catch (\Exception $e) {
        echo "Caught ".$e;
    }
    echo "\n\n";
}

if (count(get_included_files()) === 1) {
    testConvert();
}
