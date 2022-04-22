<?php

// Task.php

require_once 'FunctionalAPI.php';

class Task implements Applicative {
    
    private Closure $_fork;
    
    public function __construct(callable $fork) {
        $this->_fork = Closure::fromCallable($fork);
    }

    public function __toString() {
        return 'Task(?)';
    }

    public static function rejected($x) {
        return new Task(fn($reject, $_) => $reject($x));
    }

    // Pointed (Task a)
    public static function of($x) {
        return new Task(fn($_, $resolve) => $resolve($x));
    }

    // Functor (Task a)
    public function map($fn): Applicative {
        return new Task(fn($reject, $resolve) => $this->fork($reject, FunctionalAPI::pipe($fn, $resolve)));
    }

    // Applicative (Task a)
    public function ap(Functor | Applicative $f): Functor | Applicative {
        return $this->chain(fn($fn) => $f->map($fn));
    }

    // Monad (Task a)
    public function chain($fn) {
        return new Task(fn($reject, $resolve) => $this->fork($reject, fn($x) => $fn($x)->fork($reject, $resolve)));
    }

    public function join() {
        return $this->chain(fn($x) => $x);
    }
    
    public function fork(callable $reject, callable $resolve) {
        return call_user_func($this->_fork, $reject, $resolve);
    }
}
