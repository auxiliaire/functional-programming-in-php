<?php

// Functor.php

interface Functor {

    public function map(callable $fn): Functor;

}