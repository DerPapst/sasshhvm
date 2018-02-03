<?hh

namespace Sass;

function testCompile(): void
{
    $sass = new Sass();
    try {
        echo $sass->compile('
            $width: 50%;
            .foo {
                .bar {
                    width: $width;
                }
            }
        ');
    } catch (\Exception $e) {
        echo 'Caught '.$e."\n\n";
        if (($prev = $e->getPrevious()) != null) {
            echo 'Previous: '.$prev."\n\n";
        }
    }
}

if (count(get_included_files()) === 1) {
    testCompile();
}
