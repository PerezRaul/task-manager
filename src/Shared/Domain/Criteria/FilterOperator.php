<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Criteria;

use Src\Shared\Domain\ValueObject\Enum;

/**
 * @method static FilterOperator gt()
 * @method static FilterOperator lt()
 * @method static FilterOperator like()
 */
final class FilterOperator extends Enum
{
    public const EQUAL        = '=';
    public const NOT_EQUAL    = '!=';
    public const GT           = '>';
    public const GTE          = '>=';
    public const LT           = '<';
    public const LTE          = '<=';
    public const CONTAINS     = 'CONTAINS';
    public const NOT_CONTAINS = 'NOT_CONTAINS';
    public const IN           = 'IN';
    public const NOT_IN       = 'NOT_IN';

    private static array $containing = [self::CONTAINS, self::NOT_CONTAINS];

    public function isContaining(): bool
    {
        return in_array($this->value(), self::$containing, true);
    }
}
