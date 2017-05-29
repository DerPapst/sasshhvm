<?hh

namespace Sass\Types;

function testSassList(): void {
    echo '== '.SassList::class." ==\n";
    echo "Create SassList: ";
    echo $list = (new SassList());
    echo "\n";
    echo "Test Defaults:\n    isEmpty: ";
    var_dump($list->isEmpty());
    echo "    count: ";
    var_dump($list->count());
    echo "    Separator === SassList::SEPARATOR_COMMA: ";
    var_dump($list->getSeparator() === SassList::SEPARATOR_COMMA);
    echo "Empty SassList is valid: ";
    var_dump($list->isValid());
    echo "Add some elements: ";
    echo $list
        ->add((new SassNumber())->setValue(5)->setUnit('px'))
        ->add((new SassString())->setValue('string'))
        ->add((new SassColor())->setRGB(100, 255, 0));
    echo "\n";
    echo "Test methods for reading:\n";
    echo "    firstValue matches SassNumber 5px: ";
    var_dump($list->firstValue()?->equals((new SassNumber())->setValue(5)->setUnit('px')));
    echo "    firstKey matches is int(0): ";
    var_dump($list->firstKey() === 0);
    echo "    lastValue matches SassColor rgb(100, 255, 0): ";
    var_dump($list->lastValue()?->equals((new SassColor())->setRGB(100, 255, 0)));
    echo "    lastKey matches is int(2): ";
    var_dump($list->lastKey() === 2);
    echo "    at(0): ".$list->at(0)."\n";
    echo "    at(1): ".$list->at(1)."\n";
    echo "    at(3): ";
    try {
        echo $list->at(3)."\n";
    } catch (\Exception $e) {
        echo 'Caught '.get_class($e).' with message "'.$e->getMessage()."\"\n";
    }
    echo "    get(2): ".$list->get(2)."\n";
    echo "    get(3): ";
    var_dump($list->get(3));
    echo "    containsKey(1): ";
    var_dump($list->containsKey(1));
    echo "    containsKey(3): ";
    var_dump($list->containsKey(3));
    echo "Test methods for modifing:\n";
    echo "    set(1, SassBoolean(true)): ".$list->set(1, (new SassBoolean())->setValue(true))."\n";
    echo "    set(3, SassString(\"test\")): ";
    try {
        echo $list->set(3, (new SassString())->setValue("test"))."\n";
    } catch (\Exception $e) {
        echo 'Caught '.get_class($e).' with message "'.$e->getMessage()."\"\n";
    }
    echo "    addAll(): ";
    echo $list->addAll(ImmVector{
        (new SassString())->setValue('a test')->autoQuote(),
        new SassNull()
    })."\n";
    echo "    count is now expected to be 5: ";
    var_dump($list->count() === 5);
    echo "    setAll() with existing keys: ";
    echo $list->setAll(ImmMap{
        1 => (new SassColor()),
        4 => (new SassList()),
    })."\n";
    echo "    pop()\n";
    echo "        expected returned value is empty SassList: ";
    $item = $list->pop();
    var_dump($item->equals(new SassList()));
    echo "        count is now expected to be 4: ";
    var_dump($list->count() === 4);
    echo "    removeKey(1): ".$list->removeKey(1)." reducing count to 3: ";
    var_dump($list->count() === 3);

    echo "Iterate:\n";
    foreach ($list as $key => $value) {
        echo "    Key: ".$key." => Value: ".$value."\n";
    }

    echo "Separator:\n";
    echo "    Set to SassList::SEPARATOR_SPACE: ".$list->setSeparator(SassList::SEPARATOR_SPACE)."\n";
    echo "    getSeparator() returns SEPARATOR_SPACE: ";
    var_dump($list->getSeparator() === SassList::SEPARATOR_SPACE);
    echo "    Set to invalid separator: ";
    try {
        $list->setSeparator('-');
    } catch (\Exception $e) {
        echo 'Caught '.get_class($e).' with message "'.$e->getMessage()."\"\n";
    }
    echo "    Set to SassList::SEPARATOR_COMMA: ".$list->setSeparator(SassList::SEPARATOR_COMMA)."\n";

    echo "Validation:\n";
    echo "    isValid: ";
    var_dump($list->isValid());
    echo "    make the list invalid: ".$list->add($list)."\n";
    echo "    isValid: ";
    var_dump($list->isValid());
    echo "    try to clone the invalid list: ";
    try {
        $clone = clone $list;
    } catch (\Exception $e) {
        echo 'Caught '.get_class($e).' with message "'.$e->getMessage()."\"\n";
    }
    echo "    remove the invalid item: ".$list->removeKey(3)."\n";
    echo "Equality:\n";
    echo "    equals own identity: ";
    var_dump($list->equals($list));
    $clone = clone $list;
    echo "    equals its clone: ";
    var_dump(($list !== $clone) && $list->equals($clone) && $clone->equals($list));
    echo "    change one element of the clone: ";
    $value = $clone->at(0);
    assert($value instanceof SassNumber);
    $value->setValue(6);
    echo 'Original: '.$list.' <-> Clone: '.$clone."\n";
    echo "    still equals its clone: ";
    var_dump($list->equals($clone) || $clone->equals($list));
    echo "    equals SassColor: ";
    var_dump($list->equals(new SassColor()));

    echo "Order:\n";
    echo "    reverse: ".$list->reverse()."\n";
    echo "    lastValue matches SassNumber 5px: ";
    var_dump($list->lastValue()?->equals((new SassNumber())->setValue(5)->setUnit('px')));
    echo "    shuffle: ".$list->shuffle()."\n";

    echo "Clear list:\n";
    echo "    List is now: ".$list->clear()."\n";
    echo "    Count: ".$list->count()."\n";
    echo "\n";
}

testSassList();
