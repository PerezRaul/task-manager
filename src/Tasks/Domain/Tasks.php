<?php

declare(strict_types=1);

namespace Src\Tasks\Domain;

use Src\Shared\Domain\Collection;

final class Tasks extends Collection
{
    protected function types(): array
    {
        return [
            Task::class,
        ];
    }
}
