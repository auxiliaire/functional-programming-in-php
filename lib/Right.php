<?php

// Right.php

require_once 'Either.php';

class Right extends Either {
    
    public function isLeft() {
        return false;
    }
    
    public function isRight() {
        return true;
    }
    
    public static function of($x): Either {
        throw new Exception('`of` called on class Right (value) instead of Either (type)');
    }
    
    public function __toString() {
        return 'Right(' . var_export($this->value, true) . ')';
    }
    
    // Functor (Either a)
    public function map(callable $fn): Applicative {
        return Either::of($fn($this->value));
    }

    // Applicative (Either a)
    public function ap(Functor | Applicative $f): Functor | Applicative {
        return $f->map($this->value);
    }

    // Monad (Either a)
    public function chain(callable $fn) {
        return $fn($this->value);
    }

    public function join() {
        return $this->value;
    }

    // Traversable (Either a)
    public function sequence($of) {
        return $this->traverse($of, fn($x) => $x);
    }

    public function traverse($of, callable $fn) {
        $fn($this->value)->map(fn($x) => Either::of($x));
    }
}
