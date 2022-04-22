<?php

// Either.php

require_once 'Applicative.php';

abstract class Either implements Applicative {
    public function __construct(public $value) {}
    
    public static function of($x): Either {
        require_once 'Right.php';
        return new Right($x);
    }
    
}
