<?hh

use Sass\Sass;

function testSetIncludePaths(): void
{
    $sass = new Sass();
    try {
        $sass->setIncludePaths(array());
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
    try {
        $sass->setIncludePaths(array(__DIR__, 'tests/resources/scss', 'i/dont/exist'));
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
}

function testAddIncludePaths(): void
{
    $sass = new Sass();
    try {
        echo $sass
            ->addIncludePath('tests/resources/scss')
            ->compile('@import "herpderp";');
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
    try {
        $sass->addIncludePath('the/final/frontier');
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
}

testSetIncludePaths();
testAddIncludePaths();
