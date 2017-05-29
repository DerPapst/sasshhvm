<?hh

use Sass\Sass;

function testFormat(): void
{
    $sass = new Sass();
    $sass->setStyle(Sass::STYLE_EXPANDED);
    $sass->setLinefeed("\n+")->setIndent('⋅⋅⋅⋅');
    var_dump($sass->getLinefeed() === "\n+", $sass->getIndent() === '⋅⋅⋅⋅');
    try {
        echo $sass->compileFile('tests/resources/scss/more/links.scss')."\n";
    } catch (\Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
}

testFormat();
