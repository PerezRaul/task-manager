<?php

declare(strict_types=1);

namespace Src\Shared\Domain\HistoricalDomainEvents;

use Src\Shared\Domain\Criteria\Criteria;

interface HistoricalDomainEventRepository
{
    public function search(HistoricalDomainEventId $id): ?HistoricalDomainEvent;

    public function matching(Criteria $criteria): HistoricalDomainEvents;

    public function matchingCount(Criteria $criteria): int;
}
