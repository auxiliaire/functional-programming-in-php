<?php

// collectionDemo.php

require_once '../autoload.php';

$arr = [ 'a', 'b', 'c' ];

$obj = new stdClass();
$obj->a = 'a';
$obj->b = 'b';
$obj->c = 'c';

$itr = new ArrayIterator($arr);

echo 'drop(4, "the result") = ' . f::drop(4, "the result") . PHP_EOL;
echo 'drop(1, [ a, b, c ]) = ' . var_export(f::drop(1, $arr), true) . PHP_EOL;

echo 'head([ a, b, c ]) = ' . f::head($arr) . PHP_EOL;
echo 'head({ a, b, c }) = ' . f::head($obj) . PHP_EOL;
echo 'head(Iterator([ a, b, c ])) = ' . f::head($itr) . PHP_EOL;

echo 'init("result!") = ' . f::init("result!") . PHP_EOL;
echo 'init([ a, b, c ]) = ' . var_export(f::init($arr), true) . PHP_EOL;

echo 'last([ a, b, c ]) = ' . f::last($arr) . PHP_EOL;
echo 'last("result?") = ' . f::last('result?') . PHP_EOL;

echo 'tail("!rest") = ' . f::tail("!rest") . PHP_EOL;
echo 'tail([ a, b, c ]) = ' . var_export(f::tail($arr), true) . PHP_EOL;

echo 'take(6, "result or something else") = ' . f::take(6, "result or something else") . PHP_EOL;
echo 'take(2, [ a, b, c ]) = ' . var_export(f::take(2, $arr), true) . PHP_EOL;

echo 'OK' . PHP_EOL;
