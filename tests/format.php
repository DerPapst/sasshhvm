<?hh

function testFormat(): void
{
    $sass = new Sass();
    $sass->setStyle(\Sass::STYLE_EXPANDED);
    $sass->setLinefeed("\n+")->setIndent('⋅⋅⋅⋅');
    try {
        $css = $sass->compileFile('tests/sass/more/links.scss');
    } catch (\SassException $e) {
        // $e->getMessage() - ERROR -- , line 1: invalid top-level expression
        $css = null;
    }
    
    var_dump($css);
}

testFormat();
