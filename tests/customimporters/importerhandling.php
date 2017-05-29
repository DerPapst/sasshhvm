<?hh

namespace Sass;

function testImporterHandling(): void
{
    $sass = new Sass();

    var_dump($sass->listImporters());

    $sass->addImporter('importer-1', function (string $curPath, string $prevPath): ?Traversable<?SassImport> {
        return null;
    });
    $sass->addImporter('importer-2', function (string $curPath, string $prevPath): ?Traversable<?SassImport> {
        return null;
    }, 10);
    $sass->addImporter('importer-3', function (string $curPath, string $prevPath): ?Traversable<?SassImport> {
        return null;
    }, -3);

    var_dump($sass->listImporters());

    $sass->removeImporter('idontexist');
    $sass->removeImporter('importer-2');

    var_dump($sass->listImporters());

    $sass->setImporterPriority('importer-3', 30);

    var_dump($sass->listImporters());
}

testImporterHandling();
