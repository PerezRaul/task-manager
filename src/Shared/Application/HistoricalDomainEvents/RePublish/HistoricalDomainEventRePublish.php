<?php

declare(strict_types=1);

namespace Src\Shared\Application\HistoricalDomainEvents\RePublish;

use Src\Shared\Domain\Bus\Event\DomainEvent;
use Src\Shared\Domain\Bus\Event\EventBus;
use Src\Shared\Domain\HistoricalDomainEvents\HistoricalDomainEventId;
use Src\Shared\Domain\HistoricalDomainEvents\HistoricalDomainEventOccurredAt;
use Src\Shared\Domain\HistoricalDomainEvents\Services\HistoricalDomainEventFinder;
use Src\Shared\Domain\ValueObject\Uuid;
use Src\Shared\Infrastructure\Bus\Event\DomainEventMapping;

final class HistoricalDomainEventRePublish
{
    public function __construct(
        private HistoricalDomainEventFinder $finder,
        private DomainEventMapping $eventMapping,
        private EventBus $eventBus,
    ) {
    }

    public function __invoke(HistoricalDomainEventId $id): void
    {
        $historicalDomainEvent = $this->finder->__invoke($id);

        /** @var DomainEvent $domainEventClass */
        $domainEventClass = $this->eventMapping->for($historicalDomainEvent->name()->value());

        $domainEvent = $domainEventClass::fromPrimitives(
            $historicalDomainEvent->aggregateId()->value(),
            $historicalDomainEvent->body()->value(),
            Uuid::random()->value(),
            HistoricalDomainEventOccurredAt::now()->__toString(),
        );

        $this->eventBus->publish($domainEvent);
    }
}
