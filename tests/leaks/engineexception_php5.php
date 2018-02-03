<?hh
require_once(__DIR__.'/leak.inc');
require_once(__DIR__.'/../customfunctions/engineexception_php5.php');

runLeakTest(function (): void {
    testCFEngineException();
});
