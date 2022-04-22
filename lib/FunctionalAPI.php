<?php

spl_autoload_register(function ($class_name) {
    require_once $class_name . '.php';
});

class FunctionalAPI {
    
    // let ... in
    public static function assign(...$args) {
        return call_user_func_array(self::last($args), self::init($args));
    }
    
    public static function chain(...$args) {
        return self::curry(fn($fn, $m) => $m->chain($fn))(...$args);
    }
    
    public static function concat(...$args) {
        return self::curry(fn(string $a, string $b): string => "$a$b")(...$args);
    }
    
    public static function compose(callable ...$fns) {
        return self::pipe(...array_reverse($fns));
    }
    
    public static function curry(callable $fn) {
        $ref = new ReflectionFunction($fn);
        $given = [];
        return self::_curry($ref, $given);
    }
    
    public static function do(callable ...$fns) {
        return array_reduce($fns, fn($_, $fn) => $fn(), null);
    }
    
    public static function drop(...$args) {
        return self::curry(fn($n, $xs) => match (true) {
            is_string($xs) => substr($xs, $n),
            is_array($xs)  => array_slice($xs, $n),
            default => null
        })(...$args);
    }
    
    public static function either(...$args) {
        return self::curry(function(callable $f, callable $g, Either $e) {
            return match (true) {
                $e->isLeft() => $f($e->value),
                default      => $g($e->value)
            };
        })(...$args);
    }
    
    public static function eitherToMaybe(Either $e): Maybe {
        return match(true) {
            is_a($e, 'Right') => Maybe::of($e->value),
            default => Maybe::of()
        };
    }
    
    public static function eitherToTask(Either $e): Task {
        return f::either(fn($x) => Task::rejected($x), fn($x) => Task::of($x), $e);
    }
    
    public static function every(...$args): bool {
        return self::curry(
            fn(callable $predicate, array $xs) => self::assign(
                self::head(array_keys($xs)),
                self::head(array_values($xs)), 
                fn($k, $v) => $predicate($k, $v) ? (count($xs) > 1 ? self::every($predicate, self::tail($xs)) : true) : false
            )
        )(...$args);
    }
    
    public static function head(array | object | string $xs) {
        return match (true) {
            is_string($xs) && !empty($xs) => substr($xs, 0, 1),
            is_array($xs) && !empty($xs)  => $xs[ array_key_first($xs) ],
            is_subclass_of($xs, 'Iterator') => self::do(
                fn() => $xs->rewind(),
                fn() => $xs->valid() ? $xs->current() : null
            ),
            is_object($xs) && !empty($xs) => reset($xs),
            default => null
        };
    }
    
    public static function init(array | string $xs) {
        return match (true) {
            is_string($xs) && !empty($xs) => substr($xs, 0, -1),
            is_array($xs) && !empty($xs)  => array_slice($xs, 0, -1),
            default => null
        };
    }
    
    public static function last(array | string $xs) {
        return match (true) {
            is_string($xs) && !empty($xs) => substr($xs, -1),
            is_array($xs) && !empty($xs)  => $xs[count($xs) - 1],
            default => null
        };
    }
    
    public static function left($a): Either {
        return new Left($a);
    }
    
    public static function liftA2(...$args) {
        return self::curry(fn(callable $fn, Applicative $a1, Applicative $a2): Applicative => $a1->map($fn)->ap($a2))(...$args);
    }
    
    public static function liftA3(...$args) {
        return self::curry(fn(callable $fn, Applicative $a1, Applicative $a2, Applicative $a3): Applicative => $a1->map($fn)->ap($a2)->ap($a3))(...$args);
    }
    
    public static function log(string $msg = ''): callable {
        return self::curry(function($msg, $x) {
            echo $msg . var_export($x, true);
            return $x;
        })($msg);
    }
    
    public static function map(...$args) {
        return self::curry(fn(callable $fn, Functor $f) => $f->map($fn))(...$args);
    }
    
    public static function maybe(...$args) {
        return f::curry(fn(mixed $v, callable $f, Maybe $m) =>
            match (true) {
                $m->isNothing() => $v,
                default         => call_user_func($f, $m->get())
            }
        )(...$args);
    }
    
    public static function none(...$args): bool {
        return self::curry(
            fn(callable $predicate, array $xs) => self::assign(
                self::head(array_keys($xs)),
                self::head(array_values($xs)),
                fn($key, $value) => $predicate($key, $value) ? false : (count($xs) > 1 ? self::none($predicate, self::tail($xs)) : true)
            )
        )(...$args);
    }
    
    public static function pipe(callable ...$fns) {
        return fn(...$args) => array_reduce($fns, fn($acc, $fn) => call_user_func($fn, $acc), ...$args);
    }
    
    public static function prop(...$args) {
        return self::curry(fn(string $prop, object | array $obj) => match (true) {
            is_array($obj) && array_key_exists($prop, $obj) => $obj[$prop],
            is_object($obj) => self::_getProp($prop, $obj),
            default => null
        })(...$args);
    }
    
    public static function safeHead(array | object | string $xs): Maybe {
        return self::pipe(fn($l) => self::head($l), fn($h) => Maybe::of($h))($xs);
    }
    
    public static function safeProp(...$args): callable | Maybe {
        return self::curry(fn(string $prop, object | array $obj) => self::pipe(self::prop($prop), fn($x) => Maybe::of($x))($obj))(...$args);
    }
    
    public static function tail(array | string $xs) {
        return match (true) {
            is_string($xs) && !empty($xs) => substr($xs, 1),
            is_array($xs) && !empty($xs)  => array_slice($xs, 1),
            default => null
        };
    }
    
    public static function take(...$args) {
        return self::curry(fn($n, $xs) => match (true) {
            is_string($xs) => substr($xs, 0, $n),
            is_array($xs)  => array_slice($xs, 0, $n),
            default => null
        })(...$args);
    }
    
    // ------------ PRIVATE ------------>
    
    private static function _curry(&$ref, &$given, ...$args) {
        $given = array_merge($given, $args);
        if (count($given) < $ref->getNumberOfParameters()) {
            return fn(...$rest) => self::_curry($ref, $given, ...$rest);
        }
        return $ref->invoke(...$given);
    }
    
    private static function _asGetter(string $prop): string {
        return 'get' . ucfirst($prop);
    }
    
    private static function _getProp(string $prop, object $obj) {
        $r = new ReflectionClass($obj);
        $hasProp = $r->hasProperty($prop);
        $getter = self::_asGetter($prop);
        $hasGetter = $r->hasMethod($getter);
        return match (true) {
            $hasProp && $r->getProperty($prop)->isPublic() => $obj->$prop,
            $hasGetter && $r->getMethod($getter)->isPublic() => $obj->$getter(),
            isset($obj->$prop) => $obj->$prop,
            default => null
        };
    }
    
    private function __construct() {}

}
