<?php

declare(strict_types=1);

namespace Src\Shared\Domain\HistoricalDomainEvents;

use Src\Shared\Domain\Collection;

final class HistoricalDomainEvents extends Collection
{
    protected function types(): array
    {
        return [
            HistoricalDomainEvent::class,
        ];
    }
}
