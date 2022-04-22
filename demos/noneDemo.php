<?php

// noneDemo.php

require_once '../autoload.php';

function prettyPrintKV($k, $v) {
    echo $k . ' => ' . $v . PHP_EOL;
}

echo "every(fn(\$k, \$v) => \$k === \$v, [ 'a' => 'a', 'b' => 'b', 'c' => 'c' ]" . PHP_EOL;
var_dump(f::every(function($k, $v) { prettyPrintKV($k, $v); return $k === $v; }, [ 'a' => 'a', 'b' => 'b', 'c' => 'c' ]));

echo "every(fn(\$k, \$v) => \$k === \$v, [ 'a' => 'a', 'b' => 'd', 'c' => 'c' ]" . PHP_EOL;
var_dump(f::every(function($k, $v) { prettyPrintKV($k, $v); return $k === $v; }, [ 'a' => 'a', 'b' => 'd', 'c' => 'c' ]));

echo "none(fn(\$k, \$v) => \$k === \$v, [ 'a' => '1', 'b' => '2', 'c' => '3' ]" . PHP_EOL;
var_dump(f::none(function($k, $v) { prettyPrintKV($k, $v); return $k === $v; }, [ 'a' => '1', 'b' => '2', 'c' => '3' ]));

echo "none(fn(\$k, \$v) => \$k === \$v, [ 'a' => '1', 'b' => 'b', 'c' => '3' ]" . PHP_EOL;
var_dump(f::none(function($k, $v) { prettyPrintKV($k, $v); return $k === $v; }, [ 'a' => '1', 'b' => 'b', 'c' => '3' ]));

echo 'OK' . PHP_EOL;
