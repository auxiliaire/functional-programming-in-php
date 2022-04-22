<?php

// IntStream.php

require_once 'FunctionalAPI.php';
require_once 'Maybe.php';

class IntStream {
    
    private function __construct(private array | Generator $values) {}
    
    public function filter(callable $predicate): IntStream {
        return IntStream::of((function() use ($predicate) {
            foreach ($this->values as $val) {
                if (call_user_func($predicate, $val)) {
                    yield $val;
                }
            }
        })());
    }
    
    public function findFirst(): Maybe {
        return FunctionalAPI::safeHead($this->values);
    }
    
    public function forEach(callable $fn) {
        foreach ($this->values as $x) {
            call_user_func($fn, $x);
        }
    }
    
    public function get(): array | Generator {
        return $this->values;
    }
    
    public function map(callable $fn): IntStream {
        return IntStream::of(
            (function($values) use ($fn) {
                foreach ($values as $val) {
                    yield call_user_func($fn, $val);
                }
            })($this->values)
        );
    }
    
    public function noneMatch(callable $predicate): bool {
        $array = $this->toArray();
        return empty($array) || FunctionalAPI::none($predicate, $array);
    }
    
    public static function of(int | array | Generator $values): IntStream {
        return match (true) {
            is_int($values) => new IntStream([ $values ]),
            default => new IntStream($values),
        };
    }
    
    public static function range($startInclusive, $endExclusive): IntStream {
        return IntStream::of(
            match ($startInclusive <= $endExclusive) {
                true => (function() use ($startInclusive, $endExclusive) {
                        for ($i = $startInclusive; $i < $endExclusive; $i++) {
                            yield $i;
                        }
                    })(),
                default => (function() use ($startInclusive, $endExclusive) {
                        for ($i = $startInclusive; $i > $endExclusive; $i--) {
                            yield $i;
                        }
                    })()
            }
        );
    }
    
    public static function rangeClosed($startInclusive, $endInclusive): IntStream {
        return IntStream::of(
            match ($startInclusive <= $endInclusive) {
                true => (function() use ($startInclusive, $endInclusive) {
                        for ($i = $startInclusive; $i <= $endInclusive; $i++) {
                            yield $i;
                        }
                    })(),
                default => (function() use ($startInclusive, $endInclusive) {
                        for ($i = $startInclusive; $i >= $endInclusive; $i--) {
                            yield $i;
                        }
                    })()
            }
        );
    }
    
    public function takeWhile(callable $predicate): IntStream {
        return IntStream::of((function() use ($predicate) {
            foreach ($this->values as $val) {
                if (call_user_func($predicate, $val)) {
                    yield $val;
                } else {
                    return;
                }
            }
        })());
    }
    
    public function toArray(): array {
        return iterator_to_array($this->values, false);
    }
    
}
