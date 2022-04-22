<?php

// httpDemo.php

require_once '../autoload.php';
require_once VENDOR_PATH . 'autoload.php';

$loop = React\EventLoop\Factory::create();
$client = new React\Http\Browser($loop);

$client->get('http://www.google.com/')->then(function (Psr\Http\Message\ResponseInterface $response) {
    var_dump($response->getHeaders(), (string)$response->getBody());
});

$loop->run();

echo "OK" . PHP_EOL;
