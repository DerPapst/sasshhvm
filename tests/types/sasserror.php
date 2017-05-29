<?hh

namespace Sass\Types;

function testSassError(): void {
    echo '== '.SassError::class." ==\n";
    echo "Create SassError: ";
    echo $error = (new SassError());
    echo "\n";
    echo "Test defaults message === '': ";
    var_dump($error->getMessage() === '');
    echo "Set value to 'test message': ";
    echo $error->setMessage('test message');
    echo "\n";
    echo "Test value === 'test message': ";
    var_dump($error->getMessage() === 'test message');
    echo "Equality:\n";
    echo "    equals own identity: ";
    var_dump($error->equals($error));
    $clone = clone $error;
    echo "    equals its clone: ";
    var_dump(($error !== $clone) && $error->equals($clone) && $clone->equals($error));
    echo "    change the clone: ";
    $clone->setMessage('i am the clone');
    echo 'Original: '.$error.' <-> Clone: '.$clone."\n";
    echo "    original still equals its clone: ";
    var_dump($error->equals($clone) || $clone->equals($error));
    echo "    equals SassMap: ";
    var_dump($error->equals(new SassNull()));
    echo "\n";
}

testSassError();
