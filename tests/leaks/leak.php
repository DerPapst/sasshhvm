<?hh

function leakTest((function (): void) $fn, bool $output, bool $surpressFnOut = true): void
{
    $a = memory_get_usage(true);

    if ($surpressFnOut) ob_start();
    for ($i = 0; $i < 1000; $i++) {
        $fn();
    }
    if ($surpressFnOut) ob_end_clean();

    $b = memory_get_usage(true);
    $first_leak = $b - $a;

    if ($surpressFnOut) ob_start();
    for ($i = 0; $i < 2000; $i++) {
        $fn();
    }
    if ($surpressFnOut) ob_end_clean();

    $c = memory_get_usage(true);
    $second_leak = $c - $a;
    if ($output === false) {
        return;
    }
    $count = 0;
    if ($first_leak > 0) {
        $count = $second_leak / $first_leak;
    }
    if ($count <= 1.25) {
        echo "OK (".$count.")\n";
    } else {
        echo "Leak! First run: ".$first_leak."    Second run: ".$second_leak."    Factor: ".$count."\n";
    }
}

function runLeakTest((function (): void) $fn, bool $surpressFnOut = true): void
{
    leakTest($fn, false, $surpressFnOut);
    leakTest($fn, false, $surpressFnOut);
    leakTest($fn, true,  $surpressFnOut);
}
