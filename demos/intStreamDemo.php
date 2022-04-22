<?php

// intStreamDemo.php

require_once '../autoload.php';

function printLn() {
    return fn($x) => print("$x" . PHP_EOL);
}

function isGreaterThan(...$args) {
    return f::curry(fn($n1, $n2) => $n2 > $n1)(...$args);
}

function isEven() {
    return fn(int $number) => $number % 2 == 0;
}

function isPrime(int $number): bool {
    return $number > 1
        && IntStream::range(2, $number)
                ->noneMatch(fn($_, $index) => $number % $index == 0);
}

function doubleIt() {
    return fn($number) => $number * 2;
}

function prettyPrintList(array $list, $return = false) {
    $print = '[ ' . implode(', ', $list) . ' ]';
    return $return ? $print : print($print);
}


echo 'foreach (IntStream::range(3, 0)->get() as $i) =>' . PHP_EOL;
foreach (IntStream::range(3, 0)->get() as $i) {
    echo "$i" . PHP_EOL;
}

echo 'IntStream::rangeClosed(1, 3)->forEach(printLn()) =>' . PHP_EOL;
IntStream::rangeClosed(1, 3)->forEach(printLn());

echo 'IntStream::range(1, 4)->map(fn($x) => $x * 2)->forEach(printLn()) =>' . PHP_EOL;
IntStream::range(1, 4)->map(fn($x) => $x * 2)->forEach(printLn());

echo 'IntStream::range(1, 100)->takeWhile(fn($x) => $x < 4)->forEach(printLn()) =>' . PHP_EOL;
IntStream::range(1, 100)->takeWhile(fn($x) => $x < 4)->forEach(printLn());

echo 'IntStream::range(1, 4)->noneMatch(fn($x) => $x > 5) =>' . PHP_EOL;
var_dump(IntStream::range(1, 4)->noneMatch(fn($_, $x) => $x > 5));

echo 'IntStream::range(1, 4)->noneMatch(fn($x) => $x % 2 == 0) =>' . PHP_EOL;
var_dump(IntStream::range(1, 4)->noneMatch(fn($_, $x) => $x % 2 == 0));

echo 'isPrime(1) => ' . var_export(isPrime(1), true) . PHP_EOL;
echo 'isPrime(2) => ' . var_export(isPrime(2), true) . PHP_EOL;
echo 'isPrime(3) => ' . var_export(isPrime(3), true) . PHP_EOL;
echo 'isPrime(4) => ' . var_export(isPrime(4), true) . PHP_EOL;

$trickyList = [ 1, 2, 3, 5, 4, 6, 7, 8, 9, 10 ];
echo 'Find the double of the first even number greater than 3 in ' . prettyPrintList($trickyList, true) . ' =>' . PHP_EOL;
echo IntStream::of($trickyList)
        ->filter(function($n) { echo "isGreaterThan3($n)\n"; return isGreaterThan(3, $n); })
        ->filter(function($n) { echo "isEven($n)\n"; return isEven()($n); })
        ->map(function($n) { echo "doubleIt($n)\n"; return doubleIt()($n); })
        ->findFirst()
        . PHP_EOL;

echo 'OK' . PHP_EOL;
