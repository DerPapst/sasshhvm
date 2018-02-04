<?hh
namespace Sass;

use Sass\Types\SassValue;

class SassFakeValue extends SassValue {
    public function equals(SassValue $value): bool {
        return true;
    }

    public function __toString(): string {
        return __CLASS__;
    }
}

function testSassValueOpConstants() {
    echo 'SassValue::OP_*: ';
    $oClass = new \ReflectionClass(SassValue::class);
    $styles = [];
    foreach ($oClass->getConstants() as $c => $v) {
        if (strpos($c, 'OP_') !== false) {
            assert(is_int($v));
            $styles[$c] = (int)$v;
        }
    }
    print_r($styles);
    echo "\n";
}

function testCompareNativeOps() {
    $ops = [
        'and' => [SassValue::OP_AND, 'and'],
        'or'  => [SassValue::OP_OR,  'or'],
        'eq'  => [SassValue::OP_EQ,  '=='],
        'neq' => [SassValue::OP_NEQ, '!='],
        'gt'  => [SassValue::OP_GT,  '>' ],
        'gte' => [SassValue::OP_GTE, '>='],
        'lt'  => [SassValue::OP_LT,  '<' ],
        'lte' => [SassValue::OP_LTE, '<='],
        'add' => [SassValue::OP_ADD, '+' ],
        'sub' => [SassValue::OP_SUB, '-' ],
        'mul' => [SassValue::OP_MUL, '*' ],
        'div' => [SassValue::OP_DIV, '/' ],
        'mod' => [SassValue::OP_MOD, '%' ],
    ];
    $tests = [
        'bool' => [
            'and' => [['true', 'true'], ['true', 'false']],
            'or'  => [['true', 'false'], ['false', 'false']],
        ],
        'compare' => [
            'eq'  => [['42', '42px'], ['1%', '2%'], ['5.3em', '4.2em'], ['red', '#f00']],
            'neq' => [['42', '42px'], ['1%', '2%'], ['5.3em', '4.2em'], ['red', '#f00']],
            'gt'  => [['42', '42px'], ['1%', '2%'], ['5.3em', '4.2em']],
            'gte' => [['42', '42px'], ['1%', '2%'], ['5.3em', '4.2em']],
            'lt'  => [['42', '42px'], ['1%', '2%'], ['5.3em', '4.2em']],
            'lte' => [['42', '42px'], ['1%', '2%'], ['5.3em', '4.2em']],
        ],
        'math' => [
            'add' => [['42px', '7'], ['#a98645', '#213543'], ['1', '"2"']],
            'sub' => [['42px', '7'], ['#a98645', '#213543'], ['1', '"2"']],
            'mul' => [['42px', '7'], ['#310425', '#021521']],
            'div' => [['42px', '7'], ['#a98645', '#213543'], ['1', '"2"']],
            'mod' => [['42px', '9'], ['#a98645', '#213543']],
        ],
        'error' => [
            'lt'  => [['red', '#e00']],
            'add' => [['3px', '3em']],
            'sub' => [['rgba(0, 0, 0, 0.4)', '#185af8']],
            'mul' => [['1', '"2"']],
            'div' => [['#a98645', '#213500']],
            'mod' => [['1', '"2"']],
        ],
        'inconsistencies' => [
            'add' => [['(5, 6)', '(7, 8)']],
        ],
    ];

    $sass = (new Sass())
        ->setStyle(Sass::STYLE_EXPANDED)
        ->setIndent('    ')
        ->addFunction('exec_op($op, $a, $b)', function (ImmVector<SassValue> $args): SassValue use ($ops) {
            //var_dump($args);
            invariant($args->count() === 3, 'There should be exactly one operator two and elements in the provided arguments vector.');
            invariant($args[0] instanceof Types\SassString, 'First argument should be a SassString.');
            invariant(isset($ops[$args[0].''][0]), 'First argument should specify a valid operator.');
            $op = $ops[$args[0].''][0];
            return $args[1]->operate($op, $args[2]);
        });

    $scssTmpl = '.test {
        /*! exec_op(:op, $a, $b) == ($a :nop $b) */
        $test-custom: inspect(exec_op(:op, $a, $b));
        $test-native: inspect($a :nop $b);
        test-custom: $test-custom;
        test-native: $test-native;
        test-ok: $test-native == $test-custom;
    }';

    foreach ($tests as $category => $ct) {
        echo '== Test Category '.ucfirst($category)." ==\n";
        foreach ($ct as $op => $inputs) {
            echo $op.":\n";
            foreach ($inputs as $in) {
                invariant(isset($ops[$op][1]), 'Unable to get native operator.');
                $nativeop = $ops[$op][1];
                $scss = str_replace(
                    [':op', ':nop',    '$a',   '$b'],
                    [$op,   $nativeop, $in[0], $in[1]],
                    $scssTmpl
                );
                //echo $scss."\n\n";
                try {
                    echo '    '.str_replace("\n", "\n    ", trim($sass->compile($scss)))."\n";
                } catch (\Exception $e) {
                    echo '    '.$in[0].' '.$nativeop.' '.$in[1]."\n";
                    echo '        Caught '.get_class($e).' (C:'.$e->getCode().') on line '.$e->getLine().' with message: '.$e->getMessage()."\n";
                    $p = $e->getPrevious();
                    if ($p !== null) {
                        echo '        Previous '.get_class($p).' (C:'.$p->getCode().') on line '.$p->getLine().' with message: '.$p->getMessage()."\n";
                    }
                }
            }
            echo "\n";
        }
        echo "\n";
    }
}

function testSassOpErrors(): void {
    // Setup some values for testing errors.
    $fake = new SassFakeValue();

    $n1 = (new Types\SassNumber())->setValue(5)->setUnit('px');
    $n2= (new Types\SassNumber())->setValue(3)->setUnit('px');
    $s1 = (new Types\SassString())->setValue("4");

    $la = (new Types\SassList())
        ->add((new Types\SassNumber())->setValue(5)->setUnit('px'))
        ->add((new Types\SassString())->setValue('string'))
        ->add((new Types\SassColor())->setRGB(100, 255, 0));
    $la->add($la);

    $ln1 = (new Types\SassList())
        ->add((new Types\SassNumber())->setValue(4)->setUnit('px'));
    $ln2 = (new Types\SassList())
        ->add((new Types\SassNumber())->setValue(5)->setUnit('px'));

    $fnExecTest = function (string $desc, (function(): void) $fnTest): void {
        echo $desc."\n";
        try {
            $fnTest();
        } catch (\Exception $e) {
            echo '    Caught '.get_class($e).' (C:'.$e->getCode().') on line '.$e->getLine().' with message: '.$e->getMessage()."\n";
            $p = $e->getPrevious();
            if ($p !== null) {
                echo '     Previous '.get_class($p).' (C:'.$p->getCode().') on line '.$p->getLine().' with message: '.$p->getMessage()."\n";
            }
        }
        echo "\n";
    };

    $fnExecTest(
        'Test to convert SassFakeValue to native sass_value:',
        () ==> var_dump($n1->operate(SassValue::OP_LT, $fake))
    );
    $fnExecTest(
        'SassList equals same instance:',
        () ==> {
            var_dump($la->equals($la));
            var_dump($la->operate(SassValue::OP_EQ, $la));
        }
    );
    $fnExecTest(
        'SassList < SassList:',
        () ==> var_dump($ln1->operate(SassValue::OP_LT, $ln2))
    );
}

if (count(get_included_files()) === 1) {
    testSassValueOpConstants();
    testCompareNativeOps();
    testSassOpErrors();
}
