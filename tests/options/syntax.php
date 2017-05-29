<?hh

use Sass\Sass;

function testSyntax(): void
{
    $oClass = new ReflectionClass(Sass::class);
    $syntax = [];
    foreach ($oClass->getConstants() as $c => $v) {
        if (strpos($c, 'SYNTAX_') !== false) {
            $syntax[$c] = $v;
        }
    }
    print_r(array_keys($syntax));

    $sass = new Sass();
    var_dump($sass->getSyntax() === Sass::SYNTAX_SCSS);

    echo "\n";
    echo "== SYNTAX_SCSS ==\n";
    $sass->setSyntax(Sass::SYNTAX_SCSS);
    var_dump($sass->getSyntax() === Sass::SYNTAX_SCSS);

    try {
        echo $sass->compile(file_get_contents('tests/resources/scss/nested.scss'))."\n";
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
    try {
        echo $sass->compile(file_get_contents('tests/resources/scss/nested.sass'))."\n";
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }

    echo "== SYNTAX_SASS ==\n";
    $sass->setSyntax(Sass::SYNTAX_SASS);
    var_dump($sass->getSyntax() === Sass::SYNTAX_SASS);
    try {
        echo $sass->compile(file_get_contents('tests/resources/scss/nested.sass'))."\n";
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
    try {
        echo $sass->compile(file_get_contents('tests/resources/scss/nested.scss'))."\n";
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }

    try {
        $sass->setSyntax(9001);
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
}

testSyntax();
