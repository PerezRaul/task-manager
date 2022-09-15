<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Criteria;

use Src\Shared\Domain\Collection;

use function Lambdish\Phunctional\reduce;

final class Groups extends Collection
{
    public static function fromValues(array $values): self
    {
        return new self(array_map(self::filterBuilder(), $values));
    }

    private static function filterBuilder(): callable
    {
        return static fn(string $values) => Group::fromValues($values);
    }

    public function serialize(): string
    {
        /** @var string $reduce */
        $reduce = reduce(
            static fn(string $accumulate, Group $order) => sprintf('%s^%s', $accumulate, $order->serialize()),
            $this->items(),
            ''
        );

        return $reduce;
    }

    protected function types(): array
    {
        return [
            Group::class,
        ];
    }
}
