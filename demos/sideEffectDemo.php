<?php

// sideEffectDemo.php

$a = 'a';

function capitalA() {
    global $a;
    return strtoupper($a);
}

$a = 'b';

echo "My capital 'a' is: '" . capitalA() . "'" . PHP_EOL;

class SingletonB {
  private static $instance = null;
  
  private function __construct() {}
 
  public static function getInstance() {
    if (self::$instance == null) {
      self::$instance = new stdClass();
      self::$instance->letter = 'b';
    }
    return self::$instance;
  }
}

function workWithB($b) {
    $b->letter = 'c';
}

workWithB(SingletonB::getInstance());

echo "My capital 'b' is: '" . strtoupper(SingletonB::getInstance()->letter) . "'" . PHP_EOL;

require_once 'Stuff\Things.php';

use Stuff\Things as T;

echo T\super_secret() . PHP_EOL;

echo 'OK' . PHP_EOL;
