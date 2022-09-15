<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Criteria;

final class Order
{
    public function __construct(private OrderBy $orderBy, private OrderType $orderType)
    {
    }

    public static function createDesc(OrderBy $orderBy): Order
    {
        return new self($orderBy, new OrderType(OrderType::DESC));
    }

    public static function fromValues(string $orderBy, string $orderType): Order
    {
        return new Order(
            new OrderBy($orderBy),
            new OrderType($orderType),
        );
    }

    public function orderBy(): OrderBy
    {
        return $this->orderBy;
    }

    public function orderType(): OrderType
    {
        return $this->orderType;
    }

    public function serialize(): string
    {
        return sprintf('%s.%s', $this->orderBy->value(), $this->orderType->__toString());
    }
}
