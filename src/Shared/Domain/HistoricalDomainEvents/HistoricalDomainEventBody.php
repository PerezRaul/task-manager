<?php

declare(strict_types=1);

namespace Src\Shared\Domain\HistoricalDomainEvents;

use Src\Shared\Domain\ValueObject\ArrayValueObject;

final class HistoricalDomainEventBody extends ArrayValueObject
{
    protected function allowedTypes(): ?array
    {
        return null;
    }
}
