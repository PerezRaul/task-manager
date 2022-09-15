<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Criteria;

use DateTimeInterface;
use Src\Shared\Domain\ArrUtils;
use Src\Shared\Domain\DateUtils;
use Stringable;

class Filter
{
    final public function __construct(
        private FilterField $field,
        private FilterOperator $operator,
        private FilterValue $value
    ) {
    }

    public static function fromValues(array $values): self
    {
        return true === ArrUtils::get($values, 'or', false) ?
            new OrFilter(
                new FilterField($values['field']),
                new FilterOperator($values['operator']),
                new FilterValue($values['value'])
            ) :
            new Filter(
                new FilterField($values['field']),
                new FilterOperator($values['operator']),
                new FilterValue($values['value'])
            );
    }

    public function toValues(): array
    {
        $values = [
            'field'    => $this->field->value(),
            'operator' => $this->operator->value(),
            'value'    => $this->value->value(),
        ];

        if ($this instanceof OrFilter) {
            $values['or'] = true;
        }

        return $values;
    }

    public function field(): FilterField
    {
        return $this->field;
    }

    public function operator(): FilterOperator
    {
        return $this->operator;
    }

    public function value(): FilterValue
    {
        return $this->value;
    }

    public function serialize(): string
    {
        $value = $this->value->value();

        if (!$value instanceof Stringable) {
            if ($value instanceof DateTimeInterface) {
                $value = DateUtils::dateToString($value);
            }
            $value = json_encode($value);
        }

        return sprintf('%s.%s.%s', $this->field->value(), $this->operator->__toString(), $value);
    }
}
