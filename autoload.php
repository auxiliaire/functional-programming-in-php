<?php

define('LIBRARY_PATH',  __DIR__ . '/lib/');
define('RESOURCE_PATH', __DIR__ . '/resources/');
define('VENDOR_PATH',   __DIR__ . '/vendor/');


spl_autoload_register(function ($class_name) {
    require_once LIBRARY_PATH . $class_name . '.php';
});

class_alias('FunctionalAPI', 'f');
