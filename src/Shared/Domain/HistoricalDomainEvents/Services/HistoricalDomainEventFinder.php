<?php

declare(strict_types=1);

namespace Src\Shared\Domain\HistoricalDomainEvents\Services;

use Src\Shared\Domain\Exceptions\NotExists;
use Src\Shared\Domain\HistoricalDomainEvents\HistoricalDomainEvent;
use Src\Shared\Domain\HistoricalDomainEvents\HistoricalDomainEventId;
use Src\Shared\Domain\HistoricalDomainEvents\HistoricalDomainEventRepository;

final class HistoricalDomainEventFinder
{
    public function __construct(private HistoricalDomainEventRepository $repository)
    {
    }

    public function __invoke(HistoricalDomainEventId $id): HistoricalDomainEvent
    {
        return $this->repository->search($id) ?? throw new NotExists(HistoricalDomainEvent::class, $id);
    }
}
