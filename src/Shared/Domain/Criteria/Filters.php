<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Criteria;

use Src\Shared\Domain\ArrUtils;
use Src\Shared\Domain\Collection;

use function Lambdish\Phunctional\reduce;

class Filters extends Collection
{
    public static function fromValues(array $values): self
    {
        return true === ArrUtils::get($values, 'or', false) ?
            new OrFilters(array_map(self::filterBuilder(), ArrUtils::except($values, 'or'))) :
            new Filters(array_map(self::filterBuilder(), ArrUtils::except($values, 'or')));
    }

    public function toValues(): array
    {
        return array_map(function (Filters|OrFilters|Filter|OrFilter $filter) {
            $filters = $filter->toValues();

            if ($filter instanceof OrFilters) {
                $filters['or'] = true;
            }

            return $filters;
        }, $this->items());
    }

    private static function filterBuilder(): callable
    {
        return static fn(array $values) => ArrUtils::has($values, 'field', 'operator', 'value') ?
            Filter::fromValues($values) :
            self::fromValues($values);
    }

    public function serialize(): string
    {
        /** @var string $reduce */
        $reduce = reduce(
            static fn(string $accumulate, self|Filter $filter) => sprintf('%s^%s', $accumulate, $filter->serialize()),
            $this->items(),
            ''
        );

        return $reduce;
    }

    protected function types(): array
    {
        return [
            self::class,
            Filter::class,
        ];
    }
}
