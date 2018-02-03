<?hh
require_once(__DIR__.'/leak.inc');
require_once(__DIR__.'/../customimporters/multiimporter.php');

runLeakTest(function (): void {
    testMultipleImporters();
});
