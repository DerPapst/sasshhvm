<?hh
require_once(__DIR__.'/leak.inc');
require_once(__DIR__.'/../types/sassop.php');

runLeakTest(function (): void {
    Sass\testCompareNativeOps();
}, 50);
