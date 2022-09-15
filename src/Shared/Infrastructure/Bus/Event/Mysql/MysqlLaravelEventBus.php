<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Bus\Event\Mysql;

use Src\Shared\Domain\Bus\Event\DomainEvent;
use Src\Shared\Domain\Bus\Event\EventBus;
use Src\Shared\Domain\DateUtils;
use Src\Shared\Domain\Utils;
use Src\Shared\Infrastructure\Bus\Event\DomainEventSubscriberLocator;
use Src\Shared\Infrastructure\Bus\Event\InMemory\InMemorySymfonyEventBus;
use Illuminate\Support\Facades\DB;

use function Lambdish\Phunctional\each;

final class MysqlLaravelEventBus implements EventBus
{
    private const DATABASE_TIMESTAMP_FORMAT = 'Y-m-d H:i:s.u';

    public function __construct(private DomainEventSubscriberLocator $subscriberLocator)
    {
    }

    public function publish(DomainEvent ...$domainEvents): void
    {
        each($this->publisher(), $domainEvents);
    }

    private function publisher(): callable
    {
        return function (DomainEvent $event): void {
            DB::table('domain_events')->insert([
                'id'           => $event->eventId(),
                'aggregate_id' => $event->aggregateId(),
                'name'         => $event->eventName(),
                'body'         => Utils::jsonEncode($event->toPrimitives()),
                'occurred_at'  => DateUtils::stringToDate($event->occurredAt())->format(self::DATABASE_TIMESTAMP_FORMAT)
            ]);

            $inMemoryBus = new InMemorySymfonyEventBus(
                $this->subscriberLocator->allShouldNotQueueSubscribedTo(get_class($event)),
                $this,
            );
            $inMemoryBus->publish($event);
        };
    }

    public function historicalPublish(DomainEvent ...$domainEvents): void
    {
        each($this->historicalPublisher(), $domainEvents);
    }

    private function historicalPublisher(): callable
    {
        return function (DomainEvent $event): void {
            DB::table('historical_domain_events')->insert([
                'id'           => $event->eventId(),
                'aggregate_id' => $event->aggregateId(),
                'name'         => $event->eventName(),
                'body'         => Utils::jsonEncode($event->toPrimitives()),
                'occurred_at'  => DateUtils::stringToDate($event->occurredAt())->format(self::DATABASE_TIMESTAMP_FORMAT)
            ]);
        };
    }
}
