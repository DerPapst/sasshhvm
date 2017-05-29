<?hh

namespace Sass\Types;

function testSassWarning(): void {
    echo '== '.SassWarning::class." ==\n";
    echo "Create SassWarning: ";
    echo $warning = (new SassWarning());
    echo "\n";
    echo "Test default message === '': ";
    var_dump($warning->getMessage() === '');
    echo "Set value to 'test message': ";
    echo $warning->setMessage('test message');
    echo "\n";
    echo "Test value === 'test message': ";
    var_dump($warning->getMessage() === 'test message');
    echo "Equality:\n";
    echo "    equals own identity: ";
    var_dump($warning->equals($warning));
    $clone = clone $warning;
    echo "    equals its clone: ";
    var_dump(($warning !== $clone) && $warning->equals($clone) && $clone->equals($warning));
    echo "    change the clone: ";
    $clone->setMessage('i am the clone');
    echo 'Original: '.$warning.' <-> Clone: '.$clone."\n";
    echo "    original still equals its clone: ";
    var_dump($warning->equals($clone) || $clone->equals($warning));
    echo "    equals SassMap: ";
    var_dump($warning->equals(new SassNull()));
    echo "\n";
}

testSassWarning();
