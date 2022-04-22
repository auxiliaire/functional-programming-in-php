<?php

// composeDemo.php

require_once '../autoload.php';

// im callable add = fn($b, $a) => $a . ' ' . $b;
function add(): callable { return fn($b, $a) => $a . ' ' . $b; }

// im callable addB = curry(add)('b')
function addB(): callable { return f::curry(add())('b'); }
// im callable addC = curry(add)('c')
function addC(): callable { return f::curry(add())('c'); }
// im callable addD = curry(add)('d')
function addD(): callable { return f::curry(add())('d'); }

function bytes_to_chars(array $bytes): array {
    return array_map(fn($e) => chr($e), $bytes);
}

function bytes_to_word(array $bytes): string {
    return f::pipe(
        fn($x) => bytes_to_chars($x),
        fn($x) => array_reverse($x),
        fn($x) => join($x),
        fn($x) => ucfirst($x)
    )($bytes);
}

$bytes_to_word = f::pipe(
        fn($x) => bytes_to_chars($x),
        fn($x) => array_reverse($x),
        fn($x) => join($x),
        fn($x) => ucfirst($x)
    );

echo "compose(addD, addC, addB)('a'): \t" . f::compose(addD(), addC(), addB())('a') . PHP_EOL;
echo "pipe(addB, addC, addD)('a'): \t\t" . f::pipe(
    addB(),
    addC(),
    addD(),
    )('a') . PHP_EOL;
echo "pipe(bytes_to_chars, array_reverse, join, ucfirst)([ 111, 108, 108, 101, 104 ]): " . f::pipe(
    'bytes_to_chars',
    'array_reverse',
    'join',
    'ucfirst'
    )([ 111, 108, 108, 101, 104 ]) . PHP_EOL;
echo "bytes_to_word([ 111, 108, 108, 101, 104 ]):  " . bytes_to_word([ 111, 108, 108, 101, 104 ]) . PHP_EOL;
echo "\$bytes_to_word([ 111, 108, 108, 101, 104 ]): " . $bytes_to_word([ 111, 108, 108, 101, 104 ]) . PHP_EOL;

echo 'OK' . PHP_EOL;
