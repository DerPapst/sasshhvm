<?hh

namespace Sass\Types;

function testSassString(): void {
    echo '== '.SassString::class." ==\n";
    echo "Create SassString: ";
    echo $str = new SassString();
    echo "\n";

    echo "Test defaults\n";
    echo "    value === '': ";
    var_dump($str->getValue() === '');
    echo '    isQuoted: ';
    var_dump($str->isQuoted());
    echo '    needsQuotes: ';
    var_dump($str->needsQuotes());
    echo "Set value to 'test string': ";
    echo $str->setValue('test string');
    echo "\n";
    echo "    value === 'test string': ";
    var_dump($str->getValue() === 'test string');
    echo '    isQuoted: ';
    var_dump($str->isQuoted());

    echo "Quote Handling:\n";
    echo "    quote string: ".$str->quote()."\n";
    echo "    value === '\"test string\"': ";
    var_dump($str->getValue() === '"test string"');
    echo '    isQuoted: ';
    var_dump($str->isQuoted());
    echo "    quote string again: ".$str->quote()."\n";
    echo "    unquote string: ".$str->unquote()."\n";
    echo '    needsQuotes: ';
    var_dump($str->needsQuotes());
    echo "    autoquote: ".$str->autoQuote()."\n";
    echo '    needsQuotes: ';
    var_dump($str->needsQuotes());
    echo "New SassString with value '\"anotherstring\"': ";
    echo $str = (new SassString())->setValue('"anotherstring"');
    echo "\n";
    echo '    isQuoted: ';
    var_dump($str->isQuoted());
    echo '    needsQuotes: ';
    var_dump($str->needsQuotes());
    echo "    unquote and then autoquote: ".$str->unquote()->autoQuote()."\n";

    echo "Equality:\n";
    echo "    equals own identity: ";
    var_dump($str->equals($str));
    $clone = clone $str;
    echo "    equals its clone: ";
    var_dump(($str !== $clone) && $str->equals($clone) && $clone->equals($str));
    echo "    change the clone: ";
    $clone->setValue("herpderp");
    echo 'Original: '.$str.' <-> Clone: '.$clone."\n";
    echo "    original still equals its clone: ";
    var_dump($str->equals($clone) || $clone->equals($str));
    echo "    equals SassNumber: ";
    var_dump($str->equals(new SassNumber()));
    echo "\n";
}

testSassString();
