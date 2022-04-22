<?php

// IO.php

require_once 'FunctionalAPI.php';

class IO implements Applicative {

    private Closure $fun;
    
    public function __construct(callable $fun) {
        $this->fun = Closure::fromCallable($fun);
    }

    public function __toString() {
        return 'IO(?)';
    }

    // Pointed IO
    public static function of($x): IO {
        return new IO(fn() => $x);
    }

    // Functor IO
    public function map(callable $fn): IO {
        return new IO(FunctionalAPI::compose($fn, $this->fun));
    }

    // Applicative IO
    public function ap($f): IO {
        return $this->chain(fn($fn) => $f->map($fn));
    }

    // Monad IO
    public function chain(callable $fn): IO {
        return $this->map($fn)->join();
    }

    public function join(): IO {
        return new IO(fn() => call_user_func(
            call_user_func($this->fun)->fun
        ));
    }
    
    public function runIO() {
        return call_user_func($this->fun);
    }
}
