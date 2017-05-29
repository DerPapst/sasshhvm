<?hh

namespace Sass\Types;

function testSassBoolean(): void {
    echo '== '.SassBoolean::class." ==\n";
    echo "Create SassBoolean: ";
    echo $bool = (new SassBoolean());
    echo "\n";
    echo "Test default value === false: ";
    var_dump($bool->getValue() === false);
    echo "Set value === true: ";
    echo $bool->setValue(true);
    echo "\n";
    echo "Test value === true ";
    var_dump($bool->getValue() === true);

    echo "Equality:\n";
    echo "    equals own identity: ";
    var_dump($bool->equals($bool));
    $clone = clone $bool;
    echo "    equals its clone: ";
    var_dump(($bool !== $clone) && $bool->equals($clone) && $clone->equals($bool));
    echo "    change the clone: ";
    $clone->setValue(!$clone->getValue());
    echo 'Original: '.$bool.' <-> Clone: '.$clone."\n";
    echo "    original still equals its clone: ";
    var_dump($bool->equals($clone) || $clone->equals($bool));
    echo "    equals SassString: ";
    var_dump($bool->equals(new SassString()));
    echo "\n";
}

testSassBoolean();
