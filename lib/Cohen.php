<?php

// Cohen.php

require_once '../autoload.php';
require_once VENDOR_PATH . 'autoload.php';

use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Factory;
use React\Http\Message\Response;
use React\Http\Server;
use React\Promise\Promise;

define('GET', 'GET');
define('POST', 'POST');
define('SERVER_BOOT_MESSAGE', <<<EOT
Welcome to
   ___   ___   _   _  ____  _   _
  / __| /   | / /_/ // ___// | / /
 / /   / // // __  // ___//  |/ /
/ /__ / // // / / // /__ / |   /
\___/ \___/|_/ /_/|____/|_/|__/ v0.1b
                        Server
EOT
);

class ServerInstances {
    public function __construct(
        public \React\EventLoop\LoopInterface $loop,
        public \React\Http\Server $server,
        public \React\Socket\Server | null $socket = null
    ) {}
}

function method(ServerRequestInterface $request): string {
    return f::prop('method', $request);
}

function path(ServerRequestInterface $request): string {
    return f::pipe(f::prop('uri'), f::prop('path'))($request);
}

function responseTuple(ServerRequestInterface $request, array $responseConfig) {
    return [ $request, $responseConfig ];
}

function request($responseTuple): ServerRequestInterface {
    return $responseTuple[0];
}

function responseConfig($responseTuple): array {
    return $responseTuple[1];
}

function requestHandler(...$requestMatchers): callable {
    return fn(ServerRequestInterface $request) => new Promise(function($resolve, $reject) use ($requestMatchers, $request) {
        f::assign(
            array_reduce($requestMatchers, function($responseTuple, $matcher) {
                return f::assign($matcher(request($responseTuple)), fn($responseUpdate) => match ($responseUpdate) {
                    null    => $responseTuple,
                    default => responseTuple(request($responseTuple), $responseUpdate)
                });
            }, responseTuple($request, [ 404 ])),
            fn($responseTuple) => $resolve(new Response(...responseConfig($responseTuple)))
        );
    });
}

function isPair($k, $v): bool {
    return $k === $v || f::head($k) === ':';
}

function pathToSteps(string $path): array {
    $matches = [];
    preg_match_all("/:?[a-z0-9\-._~%!$&'()*+,;=@]+|\//", $path, $matches);
    return $matches[0];    
}

function getMatches(array $matchTuple, ServerRequestInterface $request): array {
    return f::assign(
        pathToSteps($matchTuple[1]),
        pathToSteps(path($request)),
        fn($keys, $values) => match ($matchTuple[0] === method($request) && count($keys) === count($values)) {
            true  => f::assign(array_combine($keys, $values), fn($matches) => f::every(fn($k, $v) => isPair($k, $v), $matches) ? $matches : []),
            false => []
        }
    );
}

function handle(string $method, string $path, callable $handler): callable {
    return f::curry(function (string $method, string $path, callable $handler, ServerRequestInterface $request) {
        return f::assign(getMatches([ $method, $path ], $request), fn($matches) => $matches ? $handler($request, $matches) : null);
    })($method, $path, $handler);
}

function get(string $path, callable $handler): callable {
    return handle(GET, $path, $handler);
}

function post(string $path, callable $handler): callable {
    return handle(POST, $path, $handler);
}

function response(int $status, array $headers = [], callable | string $renderer = ''): callable {
    return fn($request, $matches) => [ $status, $headers, is_callable($renderer) ? $renderer($request, $matches) : $renderer ];
}

function ok(callable | string $renderer, $contentType = 'text/plain'): callable {
    return response(200, [ 'Content-Type' => $contentType ], $renderer);
}

function loopIO(): IO {
    return IO::of(Factory::create());
}

function serverInstances($loop, $server, $socket = null): ServerInstances {
    return new ServerInstances($loop, $server, $socket);
}

function loop(ServerInstances $serverInstances): \React\EventLoop\LoopInterface {
    return f::prop('loop', $serverInstances);
}

function server(ServerInstances $serverInstances): \React\Http\Server {
    return f::prop('server', $serverInstances);
}

function socket(ServerInstances $serverInstances): Maybe {
    return Maybe::of(f::prop('socket', $serverInstances));
}

function serverLoopIO(...$args): callable {
    return f::curry(fn($requestHandler) => f::map(fn($loop) => IO::of(serverInstances($loop, new Server($loop, $requestHandler)))))(...$args);
}

function getHost(): Either {
    global $argv;
    return isset($argv[1]) ? Either::of($argv[1]) : f::left('Host not provided');
}

function getHostOrDefault(string $fallback): Either {
    return Either::of(f::either(fn() => $fallback, fn($x) => $x, getHost()));
}

function socketIO($defaultHost = '0.0.0.0:0') {
    return f::liftA2(
        f::curry(
            fn($host, $serverLoop) => f::assign(
                loop($serverLoop), 
                fn($loop) => serverInstances($loop, server($serverLoop), new \React\Socket\Server($host, $loop))
            )
        ), getHostOrDefault($defaultHost)
    );
}

function listen(...$args) {
    return f::curry(function($serverInstances) {
        socket($serverInstances)
            ->map(fn($socket) => server($serverInstances)->listen($socket));
        return $serverInstances;
    })(...$args);
}

function attachErrorHandler(...$args) {
    return f::curry(function(callable $handler, $serverInstances) {
        socket($serverInstances)
            ->map(fn($socket) => $socket->on('error', $handler));
        return $serverInstances;
    })(...$args);
}

function printStartupMessage(...$args) {
    return f::curry(function($message, $serverInstances) {
        echo $message . PHP_EOL;
        echo f::maybe(
            'Socket is not present!',
            fn(\React\Socket\Server $socket) => 'Listening on ' . str_replace('tcp:', 'http:', $socket->getAddress()),
            socket($serverInstances)
        ) . PHP_EOL;
        return $serverInstances;
    })(...$args);
}

function startLoop(...$args) {
    return f::curry(function($serverInstances) {
        loop($serverInstances)->run();
        return $serverInstances;
    })(...$args);
}

function startServer(callable $requestHandler, $host = '0.0.0.0:0', $errorHandler = 'printf', $serverBootMsg = SERVER_BOOT_MESSAGE): IO {
    return f::pipe(
        serverLoopIO($requestHandler),
        f::chain(socketIO($host)),
        f::map(listen()),
        f::map(attachErrorHandler($errorHandler)),
        f::map(printStartupMessage($serverBootMsg)),
        f::map(startLoop())
    )(loopIO());
}
