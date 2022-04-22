<?php

// Applicative.php

require_once 'Functor.php';

interface Applicative extends Functor {

    public function map(callable $fn): Applicative;
    
    public function ap(Functor | Applicative $f): Functor | Applicative;

}