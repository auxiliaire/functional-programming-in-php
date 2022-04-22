<?php

// propDemo.php

require_once '../autoload.php';

// im array myObj = [ ... ];
function myObj(): array { return [ 
    'a' => 'one',
    'b' => 'two',
    'c' => 'three',
    'd' => 'four'
]; }

class MyClass {
    function __construct(public string $a, public string $b) {}
}

// im MyClass myInst = new MyClass('first', 'second');
function myInst(): MyClass { return new MyClass('first', 'second'); }

function getA(...$args): callable | string { return f::prop('a')(...$args); }

echo "prop(array): \t\t" . getA(myObj()) . PHP_EOL;
echo "prop(instance): \t" . getA(myInst()) . PHP_EOL;

echo 'OK' . PHP_EOL;
