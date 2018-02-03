<?hh
require_once(__DIR__.'/leak.inc');
require_once(__DIR__.'/../customfunctions/convert.php');

runLeakTest(function (): void {
    Sass\testConvert();
});
