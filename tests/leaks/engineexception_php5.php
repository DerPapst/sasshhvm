<?hh
require_once(__DIR__.'/leak.php');
require_once(__DIR__.'/../customfunctions/engineexception_php5.php');

runLeakTest(function (): void {
    testCFEngineException();
});
