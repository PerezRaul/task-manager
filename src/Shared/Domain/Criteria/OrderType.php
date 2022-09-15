<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Criteria;

use Src\Shared\Domain\ValueObject\Enum;

final class OrderType extends Enum
{
    public const ASC  = 'asc';
    public const DESC = 'desc';
}
