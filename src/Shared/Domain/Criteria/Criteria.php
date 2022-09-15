<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Criteria;

final class Criteria
{
    public function __construct(
        private Filters $filters,
        private Orders $orders,
        private Groups $groups,
        private ?int $offset,
        private ?int $limit
    ) {
    }

    public function hasFilters(): bool
    {
        return $this->filters->isNonEmpty();
    }

    public function hasOrders(): bool
    {
        return $this->orders->isNonEmpty();
    }

    public function hasGroups(): bool
    {
        return $this->groups->isNonEmpty();
    }

    public function plainFilters(): array
    {
        return $this->filters->items();
    }

    public function filters(): Filters
    {
        return $this->filters;
    }

    public function orders(): Orders
    {
        return $this->orders;
    }

    public function groups(): Groups
    {
        return $this->groups;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function serialize(): string
    {
        return sprintf(
            '%s~~%s~~%s~~%s~~%s',
            $this->filters->serialize(),
            $this->orders->serialize(),
            $this->groups->serialize(),
            $this->offset,
            $this->limit
        );
    }
}
