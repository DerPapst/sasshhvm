<?hh
require_once(__DIR__.'/leak.php');
require_once(__DIR__.'/../customfunctions/convert.php');

runLeakTest(function (): void {
    Sass\testConvert();
});
