<?hh

namespace Sass\Types;

function testSassNull(): void {
    echo '== '.SassNull::class." ==\n";
    echo "Create SassNull: ";
    echo $null = new SassNull();
    echo "\n";
    echo "Equality:\n";
    echo "    equals own identity: ";
    var_dump($null->equals($null));
    $clone = clone $null;
    echo "    equals its clone: ";
    var_dump(($null !== $clone) && $null->equals($clone) && $clone->equals($null));
    echo "\n";
}

testSassNull();
