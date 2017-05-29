<?hh

namespace Sass;

function testSass2Scss(): void
{
    $oClass = new \ReflectionClass(Sass::class);
    $styles = [];
    foreach ($oClass->getConstants() as $c => $v) {
        if (strpos($c, 'SASS2SCSS_') !== false) {
            $styles[$c] = $v;
        }
    }
    ksort($styles);
    print_r(array_keys($styles));

    $scss = Sass::sass2scss(file_get_contents('tests/resources/scss/nested.sass'), Sass::SASS2SCSS_PRETTIFY_0);
    var_dump($scss);

    $scss = Sass::sass2scss(file_get_contents('tests/resources/scss/nested.sass'), Sass::SASS2SCSS_PRETTIFY_1 | Sass::SASS2SCSS_STRIP_COMMENT);
    var_dump($scss);

    $scss = Sass::sass2scss(file_get_contents('tests/resources/scss/nested.sass'), Sass::SASS2SCSS_PRETTIFY_2 | Sass::SASS2SCSS_KEEP_COMMENT);
    var_dump($scss);

    $scss = Sass::sass2scss(file_get_contents('tests/resources/scss/nested.sass'), Sass::SASS2SCSS_PRETTIFY_3 | Sass::SASS2SCSS_CONVERT_COMMENT);
    var_dump($scss);
}

testSass2Scss();
