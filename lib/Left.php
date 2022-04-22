<?php

// Left.php

require_once 'Either.php';

class Left extends Either {
  
    public function isLeft() {
        return true;
    }

    public function isRight() {
        return false;
    }

    public static function of($x): Either {
        throw new Exception('`of` called on class Left (value) instead of Either (type)');
    }

    public function __toString() {
        return 'Left(' . var_export($this->value, true) . ')';
    }

    // Functor (Either a)
    public function map(callable $fn): Applicative {
        return $this;
    }

    // Applicative (Either a)
    public function ap(Functor | Applicative $f): Functor | Applicative {
        return $this;
    }

    // Monad (Either a)
    public function chain() {
        return $this;
    }

    public function join() {
        return $this;
    }

    // Traversable (Either a)
    public function sequence($of) {
        return $of($this);
    }

    public function traverse($of, callable $fn) {
        return $of($this);
    }
}