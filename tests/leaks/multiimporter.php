<?hh
require_once(__DIR__.'/leak.php');
require_once(__DIR__.'/../customimporters/multiimporter.php');

runLeakTest(function (): void {
    testMultipleImporters();
});
