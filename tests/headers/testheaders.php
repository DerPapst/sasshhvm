<?hh

use Sass\Sass;

function testHeaders(): void
{
    $sass = new Sass();
    $sass->addHeader('comment', '/*
 * My awesome scss framework version #{$version}.
 */', 10);
    $sass->addHeader('version', '$version: "1.2.3";', 100);
    $sass->addHeader('variables', '
        $main-font: "Helvetica Neue", Verdana, sans-serif;
    ', 42);

    try {
        $css = $sass->compile('body { font-family: $main-font; }');
        echo $css."\n\n";
    } catch (Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous Exception: '.$prev."\n\n";
        }
    }
}

if (count(get_included_files()) === 1) {
    testHeaders();
}
