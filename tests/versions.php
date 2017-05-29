<?hh

namespace Sass;

function testVersions(): void
{
    echo "Sass::getLibraryVersion\n";
    var_dump(Sass::getLibraryVersion());
    echo "\nSass::getLanguageVersion\n";
    var_dump(Sass::getLanguageVersion());
    echo "\nSass::getSass2ScssVersion\n";
    var_dump(Sass::getSass2ScssVersion());
}

testVersions();
