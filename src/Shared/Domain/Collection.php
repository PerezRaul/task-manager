<?php

declare(strict_types=1);

namespace Src\Shared\Domain;

use ArrayIterator;
use Countable;
use IteratorAggregate;

abstract class Collection implements Countable, IteratorAggregate
{
    public function __construct(private array $items)
    {
        Assert::arrayOf($items, ...$this->types());
    }

    abstract protected function types(): array;

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items());
    }

    public function add(mixed ...$items): void
    {
        Assert::arrayOf($items, ...$this->types());

        array_push($this->items, ...$items);
    }

    public function count(): int
    {
        return count($this->items());
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function isNonEmpty(): bool
    {
        return $this->count() !== 0;
    }

    public function items(): array
    {
        return $this->items;
    }
}
