<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Persistence\Eloquent;

use Src\Shared\Domain\ArrUtils;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\Criteria\Filter;
use Src\Shared\Domain\Criteria\FilterOperator;
use Src\Shared\Domain\Criteria\Filters;
use Src\Shared\Domain\Criteria\Group;
use Src\Shared\Domain\Criteria\Order;
use Src\Shared\Domain\Criteria\OrFilter;
use Src\Shared\Domain\Criteria\OrFilters;
use Illuminate\Database\Eloquent\Builder;

use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\get;

/** @SuppressWarnings(PHPMD.NPathComplexity) */
final class EloquentCriteriaConverter
{
    public static function apply(Builder $eloquentBuilder, Criteria $criteria): void
    {
        self::applyFilters($eloquentBuilder, $criteria->filters());
        self::applyOrders($eloquentBuilder, $criteria);
        self::applyGroups($eloquentBuilder, $criteria);
        self::applyOffset($eloquentBuilder, $criteria);
        self::applyLimit($eloquentBuilder, $criteria);
    }

    private static function applyFilters(Builder $eloquentBuilder, Filters $filters): void
    {
        if ($filters->count() === 0) {
            return;
        }

        each(
            self::applyFilter($eloquentBuilder),
            $filters
        );
    }

    private static function applyOrders(Builder $eloquentBuilder, Criteria $criteria): void
    {
        if (!$criteria->hasOrders()) {
            return;
        }

        each(function (Order $order) use ($eloquentBuilder) {
            $eloquentBuilder->orderBy($order->orderBy()->value(), $order->orderType()->__toString());
        }, $criteria->orders()->items());
    }

    private static function applyGroups(Builder $eloquentBuilder, Criteria $criteria): void
    {
        if (!$criteria->hasGroups()) {
            return;
        }

        each(function (Group $group) use ($eloquentBuilder) {
            $eloquentBuilder->groupBy($group->groupBy()->value());
        }, $criteria->groups()->items());
    }

    private static function applyOffset(Builder $eloquentBuilder, Criteria $criteria): void
    {
        if (null === $criteria->offset()) {
            return;
        }

        $eloquentBuilder->offset($criteria->offset());
    }

    private static function applyLimit(Builder $eloquentBuilder, Criteria $criteria): void
    {
        if (null === $criteria->limit()) {
            return;
        }

        $eloquentBuilder->limit($criteria->limit());
    }

    private static function applyFilter(Builder $eloquentBuilder): callable
    {
        $casts = $eloquentBuilder->getModel()->getCasts();

        return function (Filters|Filter $filter) use ($eloquentBuilder, $casts): void {
            if ($filter instanceof Filters) {
                $method = !$filter instanceof OrFilters ? 'where' : 'orWhere';
                $eloquentBuilder->{$method}(function (Builder $subEloquentBuilder) use ($filter) {
                    self::applyFilters($subEloquentBuilder, $filter);
                });

                return;
            }

            if (get($filter->field()->value(), $casts) === 'json') {
                self::applyFilterItemJson($eloquentBuilder, $filter);

                return;
            }

            self::applyFilterItem($eloquentBuilder, $filter);
        };
    }

    /** * @SuppressWarnings(PHPMD.CyclomaticComplexity) */
    private static function applyFilterItem(Builder $eloquentBuilder, Filter $filter): void
    {
        $field    = $filter->field()->value();
        $operator = $filter->operator()->value();
        $value    = $filter->value()->value();

        if (in_array($operator, [FilterOperator::IN, FilterOperator::NOT_IN])) {
            $method = !$filter instanceof OrFilter ? 'where' : 'orWhere';

            $eloquentBuilder->{$method}(function (Builder $eloquentBuilder) use ($filter, $field, $operator, $value) {
                $method = $operator === FilterOperator::IN ? 'whereIn' : 'whereNotIn';
                if ($filter instanceof OrFilter) {
                    $method = $operator === FilterOperator::IN ? 'orWhereIn' : 'orWhereNotIn';
                }

                $hasNull = (is_array($value) && in_array(null, $value)) || null === $value;
                $value   = is_array($value) ? array_filter($value) : $value;

                if (!empty($value)) {
                    $eloquentBuilder->{$method}($field, $value);
                }

                if (!$hasNull) {
                    return;
                }

                $method = $operator === FilterOperator::IN ? 'orWhereNull' : 'orWhereNotNull';

                $eloquentBuilder->{$method}($field);
            });

            return;
        }

        $method = !$filter instanceof OrFilter ? 'where' : 'orWhere';

        if ($filter->operator()->isContaining()) {
            if (is_scalar($value)) {
                $operator = $operator === FilterOperator::CONTAINS ? 'like' : 'not like';
                $eloquentBuilder->{$method}(
                    $field,
                    $operator,
                    '%' . $value . '%'
                );
            }

            return;
        }

        $eloquentBuilder->{$method}($field, $operator, $value);
    }

    /** * @SuppressWarnings(PHPMD.CyclomaticComplexity) */
    private static function applyFilterItemJson(Builder $eloquentBuilder, Filter $filter): void
    {
        if (in_array($filter->operator()->value(), [FilterOperator::IN, FilterOperator::NOT_IN])) {
            $method     = $filter instanceof OrFilter ? 'orWhere' : 'where';
            $jsonMethod = $filter->operator()->value() === FilterOperator::IN ? 'orWhereJsonContains' : 'orWhereJsonDoesntContain';

            $eloquentBuilder->{$method}(function (Builder $builder) use ($filter, $jsonMethod) {
                $values = is_array($filter->value()->value()) ? $filter->value()->value() : [$filter->value()->value()];

                if (ArrUtils::hasValue($values, null)) {
                    $builder->orWhere($filter->field()->value(), null);
                }

                foreach ($values as $value) {
                    $builder->{$jsonMethod}($filter->field()->value(), $value);
                }
            });

            return;
        }

        $method = !$filter instanceof OrFilter ? 'where' : 'orWhere';

        if ($filter->operator()->isContaining()) {
            if (is_scalar($filter->value()->value())) {
                $operator = $filter->operator()->value() === FilterOperator::CONTAINS ? 'like' : 'not like';
                $eloquentBuilder->{$method}(
                    $filter->field()->value(),
                    $operator,
                    '%' . $filter->value()->value() . '%'
                );
            }

            return;
        }

        $eloquentBuilder->{$method}($filter->field()->value(), $filter->operator()->value(), $filter->value()->value());
    }
}
