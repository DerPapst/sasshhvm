<?hh
require_once(__DIR__.'/leak.inc');
require_once(__DIR__.'/../headers/testheaders.php');

runLeakTest(function (): void {
    testHeaders();
});
