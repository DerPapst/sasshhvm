<?hh
require_once(__DIR__.'/leak.inc');
require_once(__DIR__.'/../customfunctions/exception.php');

runLeakTest(function (): void {
    testException();
});
