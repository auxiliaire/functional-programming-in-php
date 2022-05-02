# Functional Programming in PHP

A core set of FP types and functions and the corresponding demos.

## Disclaimer

_The repository serves educational purposes and not intended for use in production!_

## Why PHP?

PHP is a very powerful programming language which offers plenty of flexibility to implement purely functional types and functions. The presented code is the result of an experiment on how pure we can get with PHP. However the goal was not to provide a toolset to PHP, but to gain a deeper understanding of the FP concepts by implementing them from scratch in a language that is supportive enough to do so. The focus is on Functional Programming and not so much on PHP. Hopefully the code is easy to follow for anyone with basic knowledge on PHP. 

## The Article Series

The Functional Programming concepts used here are presented in detail in a series of articles written on the topic, and can be found under the following link: [Introduction](https://medium.com/p/functional-programming-in-php-an-introduction-80e3f2a46e74)

This is already a six part series at the time of writing these lines, but several demos are uncovered yet and possible covered later.

## Concepts Demonstrated

* Pure functions
* Currying
* Function composition
* Pipeing
* Functor
* Applicative
* Monad
* Containers like:
  * Maybe
  * Either
  * IO
  * Task
* etc.

## Project Structure

I tried to organize the code in a clean and functional manner, but without forcing any peculiar modularization or coding style. A few organization principles were applied:

* Reusable FP code is hosted under `lib`
* Demos can be found under `demos`
* Files used by some demos are stored in `resources`
* Atomic functions are collected in `lib/FunctionalAPI`
* Types and containers are implemented with classes

## Requirements

The code heavily uses PHP8 features, therefore won't run on previous versions.

Some demos rely on [React](https://reactphp.org/) to perform HTTP requests or even create a server. This dependency can be installed easily by running `composer update` in the project root. More information on how to setup Composer itself can be found on their [website](https://getcomposer.org).

### How to run the demos

All demos are inteded for command line usage, except `serverDemo` which by nature also has a web interface. To run any of the demos, cd into `demos` and run the files with PHP like this: `php taskDemo.php` or better with some debug info: `php -d display_errors=1 taskDemo.php`.

## Cohen

Experimenting with Task and IO happened to create a nano web application framework capable of writing a webserver with just a few lines of code.
The idea was to implement something similar to Ruby's Sinatra but with purely functional code. Hence the name, refering to both Ruby's Framework and the great singer Leonard Cohen.
This little, single-file library is worth a repository and documentation on its own, but lacking the time I just post a usage example here. The source code can be found in `lib/Cohen`.

    startServer(
        requestHandler(
        
            get('/', ok(SERVER_BOOT_MESSAGE)),
            
            get('/path/sub', ok("Sub\n")),
            
            get('/path/something/:what', ok(fn($request, $matches) => 'You are at: ' . $matches[':what'] . "\n")),
        
            post('/post/:where', ok(function($request, $matches) {
                return "Posted here: {$matches[':where']}\nparsedBody: "
                    . var_export(f::prop('parsedBody', $request), true) . "\n";
            }))
        
        ), '127.0.0.1:8080'
    )->runIO();

This snippet will fire up React and exposes a few endpoints for GET requests as well as one endpoint for POST, while also captures some of the request data for dynamic processing. This would make it ideal for rapid API sketches and mocks, but in its present form it's only meant to demonstrate the expressive power of Functional Programming.

Start the server with `php -d display_errors=1 serverDemo.php` from the command line, and you can access its endpoints on [localhost](http://127.0.0.1:8080) in your preferred browser or via [Postman](https://www.postman.com/).

## Thanks

Thank you for getting through the readme, and if you'd like to learn more about Functional Programming or Programming in general, you can find me on [medium](https://auxiliaire.medium.com/).
