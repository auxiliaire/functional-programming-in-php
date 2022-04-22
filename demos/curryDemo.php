<?php

// curryDemo.php

require_once '../autoload.php';

$myfunc = function (string $a, string $b, string $c) {
    // Useless constants, that only work on top level:
    // const a = 'a';
    return $a . ' ' . $b . ' ' . $c;
};

$myc = f::curry($myfunc);

$myca = $myc('a');
$mycab = $myca('b');
$mycabc = $mycab('c');

// var_dump($myca);
// var_dump($mycab);
// var_dump($mycabc);

echo f::curry(fn() => "I don't expect arguments") . PHP_EOL;
echo "curry(myfunc)(a, b, c) => " . f::curry($myfunc)('a', 'b', 'c') . PHP_EOL;
echo "myc(a, b, c) \t => \t" . $myc('a', 'b', 'c') . PHP_EOL;
echo "myc(a)(b)(c) \t => \t" . $myc('a')('b')('c') . PHP_EOL;
echo "myc(a, b)(c) \t => \t" . $myc('a', 'b')('c') . PHP_EOL;
echo "myc(a)(b, c) \t => \t" . $myc('a')('b', 'c') . PHP_EOL;
echo "myca(b, c) \t => \t" . $myca('b', 'c') . PHP_EOL;
echo "myca(b)(c) \t => \t" . $myca('b')('c') . PHP_EOL;
echo "mycab(c) \t => \t" . $mycab('c') . PHP_EOL;
echo "mycabc \t\t => \t" . $mycabc . PHP_EOL;

// immutable

// im callable greet = fn(callable $form, string $name): string => $form() . ', ' . $name . '!';
function greet(): callable { return fn(callable $form, string $name): string => $form() . ', ' . $name . '!'; }

// im callable formal = fn(): string => "Good day";
function formal(): callable { return fn(): string => "Good day"; }
// im callable informal = fn(): string => "Hello";
function informal(): callable { return fn(): string => "Hello"; }

// im callable greetFormally = curry(greet)(formal);
function greetFormally(): callable { return f::curry(greet())(formal()); }
// im callable greetInformally = curry(greet)(informal);
function greetInformally(): callable { return f::curry(greet())(informal()); }

// im string tulin = "Tülin";
function tulin(): string { return "Tülin"; }

// im string girl = tulin;
function girl(): string { return tulin(); }

// reassignment does not work:
// function girl(): string { return "Somebody else"; }

// greet(formal, tulin)
echo "Immutable greeting: " . greet()(formal(), tulin()) . PHP_EOL;
// greetFormally(tulin)
echo greetFormally()(tulin()) . PHP_EOL;
// greetInformally(girl)
echo greetInformally()(girl()) . PHP_EOL;

echo "OK" . PHP_EOL;
