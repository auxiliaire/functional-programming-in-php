<?php

// eitherDemo.php

require_once '../autoload.php';

define('valid_json', '{ "stuff": [ "first", "second", "third" ] }');
define('invalid_json', 'utter bollocks');

function parseJson(...$args) {
    return f::curry(function($json): Either {
        $obj = json_decode($json);
        
        return match ($obj) {
            null    => f::left(json_last_error_msg()),
            default => Either::of($obj)
        };
    })(...$args);
}

function getFirstStuff() {
    return f::pipe(
        f::prop('stuff'),
        fn($list) => f::head($list)
    );
}

echo 'Either::of("this")->map(fn($str) => "Use $str!")' . "\t\t    => \t" . Either::of('this')->map(fn($str) => "Use $str!") . PHP_EOL;

echo 'left("ignore this")->map(fn($str) => "Don\'t $str!")' . "\t\t    => \t" . f::left('ignore this')->map(fn($str) => "Don't $str!") . PHP_EOL;

echo "Either::of([ 'name' => 'Nilüfer', 'age' => 9 ])->map(prop('name'))  =>\t" . Either::of([ 'name' => 'Nilüfer', 'age' => 9 ])->map(f::prop('name')) . PHP_EOL;

echo "f::left('My error message')->map(f::prop('name')) \t\t    => \t" . f::left('My error message')->map(f::prop('name')) . PHP_EOL;

echo "parseJson(valid_json)->map(getFirstStuff()) \t\t\t    => \t" . parseJson(valid_json)->map(getFirstStuff()) . PHP_EOL;

echo "parseJson(invalid_json)->map(getFirstStuff()) \t\t\t    => \t" . parseJson(invalid_json)->map(getFirstStuff()) . PHP_EOL;

echo "pipe( parseJson, either( id, getFirstStuff ), print )(valid_json)   => \t";
f::pipe(
    parseJson(),
    f::either(fn($x) => $x, getFirstStuff()),
    fn($x) => print($x . PHP_EOL)
)(valid_json);

echo "pipe( parseJson, either( id, getFirstStuff ), print )(invalid_json) => \t";
f::pipe(
    parseJson(),
    f::either(fn($x) => $x, getFirstStuff()),
    fn($x) => print($x . PHP_EOL)
)(invalid_json);

echo "eitherToMaybe(Either::of('something')) \t\t\t\t    => \t" . f::eitherToMaybe(Either::of('something')) . PHP_EOL;
echo "eitherToMaybe(left('nothing')) \t\t\t\t\t    => \t" . f::eitherToMaybe(f::left('nothing')) . PHP_EOL;

echo 'OK' . PHP_EOL;
