<?hh

namespace Sass;

function testCompileErrorString(): void
{
    $source = ".herp {\n    color: red;\n    .derp {\n        asd\n    }\n}";
    $sass = new Sass();
    try {
        var_dump($sass->compile($source));
    } catch (SassException $se) {
        echo 'Caught '.$se."\n\n";
        var_dump(
            $se->getSourceFile(),
            $se->getSourceLine(),
            $se->getSourceColumn(),
            $se->getFormattedMessage()
        );
        echo "\n\n";
    }
}

function testCompileErrorFile(): void
{
    $sass = new Sass();
    try {
        var_dump($sass->compileFile('tests/resources/scss/more/borked.scss'));
    } catch (\Exception $se) {
        echo 'Caught '.$se."\n\n";
    }
}

testCompileErrorString();
testCompileErrorFile();
