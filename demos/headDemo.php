<?php

// headDemo.php

require_once '../autoload.php';

$arr = [ 'a', 'b', 'c' ];

$obj = new stdClass();
$obj->a = 'a';
$obj->b = 'b';
$obj->c = 'c';

$itr = new ArrayIterator($arr);

echo 'head([ a, b, c ]) = ' . f::head($arr) . PHP_EOL;
echo 'head({ a, b, c }) = ' . f::head($obj) . PHP_EOL;
echo 'head(Iterator([ a, b, c ])) = ' . f::head($itr) . PHP_EOL;

echo 'OK' . PHP_EOL;
