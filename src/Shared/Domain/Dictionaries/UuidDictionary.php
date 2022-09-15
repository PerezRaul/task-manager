<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Dictionaries;

use ArrayIterator;
use Src\Shared\Domain\ArrUtils;
use Src\Shared\Domain\ValueObject\Uuid;

final class UuidDictionary
{
    private array $dictionary = [];

    public function get(string $name, mixed $default = null): mixed
    {
        if (!ArrUtils::has($this->dictionary, $name)) {
            ArrUtils::set($this->dictionary, $name, $default ?? Uuid::random()->value());
        }

        return ArrUtils::get($this->dictionary, $name);
    }

    public function isEmpty(string $name): bool
    {
        if (!ArrUtils::has($this->dictionary, $name)) {
            return true;
        }

        return empty(ArrUtils::get($this->dictionary, $name, []));
    }

    public function each(string $name, callable $callback, int $levels = 1): void
    {
        $iterator = $this->makeIterator($name);

        while ($iterator->valid()) {
            if ($levels === 1) {
                $callback($iterator->current());
            }

            if (is_array($iterator->current())) {
                $keys = array_keys($iterator->current());
                foreach ($keys as $key) {
                    $this->each($name . '.' . $key, $callback, $levels - 1);
                }
            }

            $iterator->next();
        }
    }

    public function random(string $name, mixed $default = null, int $levels = 1): mixed
    {
        if (!ArrUtils::has($this->dictionary, $name)) {
            return $default;
        }

        /** @var array $results */
        $results = ArrUtils::get($this->dictionary, $name);

        for ($i = 0; $i < $levels; $i++) {
            if (!is_array($results)) {
                return $results;
            }

            $results = ArrUtils::random($results);
        }

        return $results;
    }

    private function makeIterator(string $name): ArrayIterator
    {
        if (!ArrUtils::has($this->dictionary, $name)) {
            return new ArrayIterator([]);
        }

        /** @var array $results */
        $results = ArrUtils::get($this->dictionary, $name);

        return new ArrayIterator($results);
    }
}
