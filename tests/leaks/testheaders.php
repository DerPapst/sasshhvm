<?hh
require_once(__DIR__.'/leak.php');
require_once(__DIR__.'/../headers/testheaders.php');

runLeakTest(function (): void {
    testHeaders();
});
