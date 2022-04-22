<?php

// Maybe.php

require_once 'Applicative.php';

class Maybe implements Applicative {

    public function __construct(private $value = null) { }

    public function isNothing(): bool {
        return $this->value === null;
    }

    public function isJust(): bool {
        return !$this->isNothing();
    }

    public function __toString() {
        return $this->isNothing() ? 'Nothing' : "Just(" . var_export($this->value, true) . ")";
    }
    
    public function get() {
        return $this->value;
    }

    // Pointed Maybe
    public static function of($x = null) {
        return new Maybe($x);
    }

    // Functor Maybe
    public function map(callable $fn): Applicative {
        return $this->isNothing() ? $this : Maybe::of($fn($this->value));
    }

    // Applicative Maybe
    public function ap(Functor | Applicative $f): Functor | Applicative {
        return $this->isNothing() ? $this : $f->map($this->value);
    }

    // Monad Maybe
    public function chain(callable $fn) {
        return $this->map($fn)->join();
    }

    public function join() {
        return $this->isNothing() ? $this : $this->value;
    }

    // Traversable Maybe
    public function sequence(callable $of) {
        return $this->traverse($of, fn($x) => $x);
    }

    public function traverse(callable $of, callable $fn) {
        return $this->isNothing() ? $of($this) : $fn($this->value)->map(fn($x) => Maybe::of($x));
    }
}
