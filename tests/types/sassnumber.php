<?hh

namespace Sass\Types;

function testSassNumber(): void {
    echo '== '.SassNumber::class." ==\n";
    echo "Create SassNumber: ";
    echo $nr = new SassNumber();
    echo "\n";

    echo "Test defaults\n";
    echo "    value === 0: ";
    var_dump($nr->getValue() === 0);
    echo "    unit === '': ";
    var_dump($nr->getUnit() === '');
    echo "Set value to 10 and unit to 'px': ";
    echo $nr->setValue(10)->setUnit('px');
    echo "\n";
    echo '    value === 10: ';
    var_dump($nr->getValue() === 10);
    echo "    unit === 'px': ";
    var_dump($nr->getUnit() === 'px');
    echo "New number with value 53.5 and unit '%': ";
    echo $nr = (new SassNumber())->setValue(53.5)->setUnit('%');
    echo "\n";
    echo '    value === 53.5: ';
    var_dump($nr->getValue() === 53.5);
    echo "    unit === '%': ";
    var_dump($nr->getUnit() === '%');

    echo "Equality:\n";
    echo "    equals own identity: ";
    var_dump($nr->equals($nr));
    $clone = clone $nr;
    echo "    equals its clone: ";
    var_dump(($nr !== $clone) && $nr->equals($clone) && $clone->equals($nr));
    echo "    change the clone: ";
    $clone->setValue(100);
    echo 'Original: '.$nr.' <-> Clone: '.$clone."\n";
    echo "    original still equals its clone: ";
    var_dump($nr->equals($clone) || $clone->equals($nr));
    echo "    equals SassNull: ";
    var_dump($nr->equals(new SassNull()));
    echo "\n";
}

testSassNumber();
