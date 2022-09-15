<?php

declare(strict_types=1);

namespace Src\Tasks\Domain\Filters;

use Src\Shared\Domain\Criteria\FilterParserFactory;

final class TaskFilterParserFactory extends FilterParserFactory
{
    protected static function mapping(): array
    {
        return [
            'search' => SearchFilterParser::class,
        ];
    }
}
