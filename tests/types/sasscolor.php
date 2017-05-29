<?hh

namespace Sass\Types;

function testSassColor(): void
{
    echo '== '.SassColor::class." ==\n";
    echo "Create SassColor: ";
    echo $color = (new SassColor());
    echo "\n";
    echo "Test defaults:\n";
    echo "    value === rgb(0,0,0): ";
    var_dump($color->getRGB() === ['r' => 0, 'g' => 0, 'b' => 0]);
    echo "    alpha === 1.0: ";
    var_dump($color->getAlpha() === 1.0);
    echo "Set rgb color to SassColorRGB(248, 140, 30) and alpha 0.5: ";
    echo $color->setRGBFromShape(shape('r' => 247, 'g' => 142, 'b' => 35), 0.5);
    echo "\n";
    echo "Verify new values: ";
    var_dump($color->getRGB() === ['r' => 247, 'g' => 142, 'b' => 35] && $color->getAlpha() === 0.5);
    echo "Color conversion:\n";
    $hsl = $color->getHSL();
    echo "    as SassColorHSL: ".json_encode(['h' => round($hsl['h'], 3), 's' => round($hsl['s'], 3), 'l' => round($hsl['l'], 3)])."\n";
    echo "    converted back to SassColorRGB: ".json_encode(SassColor::hslToRgb($hsl['h'], $hsl['s'], $hsl['l']))."\n";
    echo "    Create a new color from SassColorHSL and the old alpha value: ";
    echo $color = (new SassColor())->setHSLFromShape($hsl, $color->getAlpha());
    echo "\n";

    echo "Equality:\n";
    echo "    equals own identity: ";
    var_dump($color->equals($color));
    $clone = clone $color;
    echo "    equals its clone: ";
    var_dump(($color !== $clone) && $color->equals($clone) && $clone->equals($color));
    echo "    change the clone: ";
    $clone->setAlpha(0.1);
    echo 'Original: '.$color.' <-> Clone: '.$clone."\n";
    echo "    original still equals its clone: ";
    var_dump($color->equals($clone) || $clone->equals($color));
    echo "    equals SassBoolean: ";
    var_dump($color->equals(new SassBoolean()));
    echo "\n";
}

testSassColor();
