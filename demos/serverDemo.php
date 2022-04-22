<?php

// serverDemo.php

require_once '../autoload.php';
require_once LIBRARY_PATH . 'Cohen.php';

startServer(
    requestHandler(
    
        get('/', ok(SERVER_BOOT_MESSAGE)),
        
        get('/path/sub', ok("Sub\n")),
        
        get('/path/something/:what', ok(fn($request, $matches) => 'You are at: ' . $matches[':what'] . "\n")),
        
        post('/post/:where', ok(function($request, $matches) {
            return "Posted here: {$matches[':where']}\nparsedBody: " . var_export(f::prop('parsedBody', $request), true) . "\n";
        }))
        
    ), '127.0.0.1:8080'
)->runIO();

// $loop = Factory::create();

/*
$server = new Server($loop, requestHandler(
            
            get('/path/sub', ok("Sub\n")),
            
            get('/path/something/:what', ok(fn($request, $matches) => 'You are at: ' . $matches[':what'] . "\n")),
            
            post('/post/:where', ok(function($request, $matches) {
                return "Posted here: {$matches[':where']}\nparsedBody: " . var_export(f::prop('parsedBody', $request), true) . "\n";
            }))
            
));
//*/

// $socket = new \React\Socket\Server(isset($argv[1]) ? $argv[1] : '0.0.0.0:0', $loop);
// $server->listen($socket);

// $socket->on('error', 'printf');

// echo 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()) . PHP_EOL;

// $loop->run();