<?hh
require_once(__DIR__.'/leak.inc');
require_once(__DIR__.'/../compile.php');

runLeakTest(function (): void {
    Sass\testCompile();
});
