<?php

// ioDemo.php

require_once '../autoload.php';

function randIO(): IO {
    return new IO(fn(): int => random_int(0, 100));
}

function messageRand(): callable {
    return f::concat("We generated a random number for you: ");
}

function messageTwoRand(): callable {
    return f::curry(fn($a, $b): string => "We generated two random numbers for you: $a, $b");
}

function twoRandIO(): IO {
    return f::liftA2(messageTwoRand(), randIO(), randIO());
    // equivalent to:
    // IO::of( messageTwoRand() )->ap(randIO())->ap(randIO());
}

function printIO(): callable {
    return fn($x): IO => new IO(fn() => print($x . PHP_EOL));
}

function readlineIO(): callable {
    return fn(): IO => new IO(fn(): string | false => readline("Do you like it? "));
}

function messageReply(): callable {
    return f::concat("You replied: ");
}

function fileContent(...$args): IO {
    return f::curry(fn($fileName) => new IO(fn(): string | false => file_get_contents($fileName)))(...$args);
}

function file1(): IO {
    return fileContent(RESOURCE_PATH . 'test1.txt');
}

function file2(): IO {
    return fileContent(RESOURCE_PATH . 'test2.txt');
}

function twoTestContents(): IO {
    return f::liftA2(f::curry(fn($content1, $content2): string => "We've read the contents of file 1 and 2: '$content1', '$content2'"), file1(), file2());
}

// IMPURE
/*
f::pipe(
    f::map( messageRand() ),
    f::chain( printIO() ),
    f::chain( readlineIO() ),
    f::map( messageReply() ),
    f::chain( printIO() )
)(randIO())->runIO();

// equivalent to:
randIO()
    ->map(messageRand())
    ->chain(printIO())
    ->chain(readlineIO())
    ->map(messageReply())
    ->chain(printIO())
    ->runIO();
//*/

f::pipe(
    f::chain( printIO() ),
    f::chain( readlineIO() ),
    f::map( messageReply() ),
    f::chain( printIO() )
)(twoRandIO())->runIO();

f::pipe(
    f::chain( printIO() )
)(twoTestContents())->runIO();

echo 'OK' . PHP_EOL;
