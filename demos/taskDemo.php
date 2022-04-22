<?php

// taskDemo.php

require_once '../autoload.php';
require_once VENDOR_PATH . 'autoload.php';


$loop = React\EventLoop\Factory::create();
$client = new React\Http\Browser($loop);

function messageOfTwo(string $msg): callable {
    return f::curry(fn($a, $b): string => "$msg $a, $b");
}

function printIO(): callable {
    return fn($x): IO => new IO(fn() => print($x . PHP_EOL));
}

function fileContentFork(string $filename, callable $reject, callable $resolve) {
    $contents = file_get_contents($filename);
    return match ($contents) {
        false => call_user_func($reject),
        default => call_user_func($resolve, $contents)
    };
}

function fileContent(...$args): Task {
    return f::curry(fn($filename) => new Task(fn($reject, $resolve) => fileContentFork($filename, $reject, $resolve)))(...$args);
}

// const randi = random_int(0, 1);
define('randi', random_int(0, 1));

function file1(): Task {
    define('test1Name', 'test1' . (fn() => '.txt')());
    return fileContent(RESOURCE_PATH . test1Name);
}

function file2(): Task {
    define('test2Name', str_replace('1', '2', test1Name));
    return fileContent(RESOURCE_PATH . test2Name);
}

function twoTestContents(): Task {
    return f::liftA2(f::curry(fn($content1, $content2): string => "We've read the contents of file 1 and 2: '$content1', '$content2'"), file1(), file2());
}

function getPage(React\Http\Browser $client, string $url): Task {
    return new Task(fn($reject, $resolve) => $client->get($url)->then(
        function (Psr\Http\Message\ResponseInterface $response) use ($resolve) {
            call_user_func($resolve, (string)$response->getBody());
        },
        fn($reason) => call_user_func($reject, $reason))
    );
}

function getGoogle(React\Http\Browser $client): Task {
    return getPage($client, 'http://www.google.com/');
}

function getBing(React\Http\Browser $client): Task {
    return getPage($client, 'http://www.bing.com/');
}

function lengthMessage(string $label) {
    return f::pipe(
        fn($res) => strlen($res),
        fn($len) => "$label has a length of $len characters at the moment",
        printIO()
    );
}

function title(string $content): string {
    preg_match("/<title>(.*)<\/title>/is", $content, $matches);
    return 'Title: ' . $matches[1];
}

function twoRequests(React\Http\Browser $client): Task {
    return f::liftA2(f::curry(fn($contentGoogle, $contentBing): string => title($contentGoogle) . PHP_EOL . title($contentBing) . PHP_EOL), getGoogle($client), getBing($client));
}

// IMPURE

twoTestContents()->fork(
    fn() => print('Could not read files' . PHP_EOL), 
    fn($result) => printIO()($result)->runIO()
);

getGoogle($client)->fork(
    fn($reason) => print('Could not initiate HTTP request (reason: ' . $reason . ')' . PHP_EOL),
    fn($result) => lengthMessage('Google')($result)->runIO()
);

getBing($client)->fork(
    fn($reason) => print('Could not initiate HTTP request (reason: ' . $reason . ')' . PHP_EOL),
    fn($result) => lengthMessage('Bing')($result)->runIO()
);

twoRequests($client)->fork(
    fn($reason) => print('Could not initiate HTTP request (reason: ' . $reason . ')' . PHP_EOL),
    fn($result) => printIO()($result)->runIO()
);

$loop->run();

echo 'OK' . PHP_EOL;
