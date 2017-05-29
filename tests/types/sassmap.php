<?hh

namespace Sass\Types;

function testSassMap(): void {
    echo '== '.SassMap::class." ==\n";
    echo "Create SassMap: ";
    echo $map = (new SassMap());
    echo "\n";
    echo "Test Defaults:\n    isEmpty: ";
    var_dump($map->isEmpty());
    echo "    count: ";
    var_dump($map->count());
    echo "    isValid: ";
    var_dump($map->isValid());

    echo "Add some elements: ";
    echo $map->addAll(ImmVector {
        (new SassMapPair())->set(3, true),
        (new SassMapPair())->set('herp', 'derp'),
        (new SassMapPair())->set((new SassColor())->setHSL(30.0, 1.0, 0.5), null)
    })."\n";

    echo "Create a 2nd map: ";
    $map2 = (new SassMapPair())->set(5, 'foo')->toMap()
        ->set((new SassNumber())->setValue(3), (new SassBoolean())->setValue(false));
    echo $map2."\n";
    echo "Merge both maps: ";
    $map->setAll($map2);
    echo $map."\n";

    echo "Test methods for reading:\n";
    echo "    firstKey: ".$map->firstKey()."\n";
    echo "    firstValue: ".$map->firstValue()."\n";
    echo "    lastKey: ".$map->lastKey()."\n";
    echo "    lastValue: ".$map->lastValue()."\n";
    echo "    keys:\n";
    echo "        is SassList: ";
    var_dump($map->keys() instanceof SassList);
    echo "        contents: ".$map->keys()."\n";
    echo "    values:\n";
    echo "        is SassList: ";
    var_dump($map->values() instanceof SassList);
    echo "        contents: ".$map->values()."\n";
    echo "    at(SassValue::cs(5)): ".$map->at(SassValue::cs(5))."\n";
    echo "    at(SassValue::cs('5')): ";
    try {
        echo $map->at(SassValue::cs('5'))."\n";
    } catch (\Exception $e) {
        echo 'Caught '.get_class($e).' with message "'.$e->getMessage()."\"\n";
    }
    echo "    get(SassValue::cs('herp')): ".$map->get(SassValue::cs('herp'))."\n";
    echo "    get(SassValue::cs('5')): ";
    var_dump($map->get(SassValue::cs('5')));
    echo "    containsKey(SassValue::cs(3)): ";
    var_dump($map->containsKey(SassValue::cs(3)));
    echo "    containsKey(SassValue::cs('5')): ";
    var_dump($map->contains(SassValue::cs('5')));

    echo "Iterate:\n";
    foreach ($map as $key => $value) {
        echo "    Key: ".$key." => Value: ".$value."\n";
    }

    echo "Validation:\n";
    echo "    isValid: ";
    var_dump($map->isValid());
    echo "    make the map invalid: ";
    echo $map->add((new SassMapPair())->set(3, (new SassList())->add($map)))."\n";
    echo "    isValid: ";
    var_dump($map->isValid());
    echo "    try to clone the invalid list: ";
    try {
        $clone = clone $map;
    } catch (\Exception $e) {
        echo 'Caught '.get_class($e).' with message "'.$e->getMessage()."\"\n";
    }
    echo "    remove the invalid item: ".$map->remove(SassValue::cs(3))."\n";

    echo "Equality:\n";
    echo "    equals own identity: ";
    var_dump($map->equals($map));
    $clone = clone $map;
    echo "    equals its clone: ";
    var_dump(($map !== $clone) && $map->equals($clone) && $clone->equals($map));
    echo "    change one element of the clone: ";
    $value = $clone->at(SassValue::cs(5));
    assert($value instanceof SassString);
    $value->setValue('bar');
    echo 'Original: '.$map.' <-> Clone: '.$clone."\n";
    echo "    still equals its clone: ";
    var_dump($map->equals($clone) || $clone->equals($map));
    echo "    equals SassList: ";
    var_dump($map->equals(new SassList()));

    echo "Clear map:\n";
    echo "    Map is now: ".$map->clear()."\n";
    echo "    Count: ".$map->count()."\n";
    echo "\n";
}

testSassMap();
