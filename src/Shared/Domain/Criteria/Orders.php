<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Criteria;

use Src\Shared\Domain\Collection;

use function Lambdish\Phunctional\reduce;

final class Orders extends Collection
{
    public static function fromValues(array $values): self
    {
        return new self(array_map(self::filterBuilder(), $values));
    }

    private static function filterBuilder(): callable
    {
        return static fn(array $values) => is_array(current($values)) ?
            self::fromValues($values) :
            Order::fromValues(...$values);
    }

    public function serialize(): string
    {
        /** @var string $reduce */
        $reduce = reduce(
            static fn(string $accumulate, Order $order) => sprintf('%s^%s', $accumulate, $order->serialize()),
            $this->items(),
            ''
        );

        return $reduce;
    }

    protected function types(): array
    {
        return [
            Order::class,
        ];
    }
}
