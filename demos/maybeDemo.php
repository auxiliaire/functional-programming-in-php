<?php

// maybeDemo.php

require_once '../autoload.php';

function randMaybe(): Maybe {
    return Maybe::of(random_int(0, 1) ? 'Value' : null);
}

function messageTwoRand(): callable {
    return f::curry(fn($a, $b): string => "We generated two random values for you: $a, $b");
}

function getStreetName(...$args) {
    return f::pipe(
        f::safeProp('address'),
        f::chain(f::safeProp('street')),
        f::chain(f::safeProp('name'))
    )(...$args);
}

function toEula() {
    return f::pipe(
        fn($s) => [ f::head($s), f::tail($s) ],
        fn($a) => strtolower($a[0]) . strtoupper($a[1]),
        fn($s) => strrev($s),
        fn($s) => $s . '1'
    );
}

$rand = randMaybe();

const user = [
    'id' => 1,  
    'name' => 'Albert',  
    'address' => [  
        'street' => [
            'number' => 22,  
            'name' => 'Walnut St',  
        ],  
    ],  
];

// toEula()(null); // crash!

echo "Maybe in action: " . $rand . PHP_EOL;
echo "map(): " . $rand->map(fn($s) => [ f::head($s), f::tail($s) ])
                      ->map(fn($a) => strtolower($a[0]) . strtoupper($a[1]))
                      ->map(fn($s) => strrev($s))
                      ->map(fn($s) => $s . '1')
                      . PHP_EOL;
echo "map(): " . $rand->map(toEula())
                      . PHP_EOL;
echo "Toast: " . f::maybe('Old API failed again, please try again after you finished steering your latte macchiato', fn($s) => toEula()($s) . ' is ready', $rand) . PHP_EOL;
echo "ap(): " . Maybe::of(fn($s) => strtoupper($s))->ap($rand) . PHP_EOL;
echo "ap(2): " . f::liftA2(messageTwoRand(), $rand, Maybe::of('Stable')) . PHP_EOL;
echo "safeHead([]): " . f::safeHead([]) . PHP_EOL;
echo "safeHead([ a, b, c ]): " . f::safeHead([ 'a', 'b', 'c' ]) . PHP_EOL;
echo "safeProp('property', {}): " . f::safeProp('property', new stdClass()) . PHP_EOL; 
echo "safeProp('property', [ 'property' => 'sum' ]): " . f::safeProp('property', [ 'property' => 'sum' ]) . PHP_EOL;

echo "Consider: user = " . PHP_EOL;
print_r(user);

echo "pipe( safeProp('address'), chain( safeProp('street') ), chain( safeProp('name') )(user):    "
    . getStreetName(user) . PHP_EOL;

echo "pipe( safeProp('address'), chain( safeProp('street') ), chain( safeProp('name') )(Nothing): "
    . getStreetName(Maybe::of(null)) . PHP_EOL;

echo "Maybe::of('a')->map('double')->map('double')->map('double'): " . Maybe::of('a')->map(fn($x) => $x . $x)->map(fn($x) => $x . $x)->map(fn($x) => $x . $x) . PHP_EOL;
echo "Maybe::of(null)->map('double')->map('double')->map('double'): " . Maybe::of(null)->map(fn($x) => $x . $x)->map(fn($x) => $x . $x)->map(fn($x) => $x . $x) . PHP_EOL;

echo 'OK' . PHP_EOL;

