<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Bus\Event\Mysql;

use DateTimeImmutable;
use Src\Shared\Domain\Bus\Event\DomainEvent;
use Src\Shared\Domain\DateUtils;
use Src\Shared\Domain\Utils;
use Src\Shared\Infrastructure\Bus\Event\DomainEventMapping;
use Illuminate\Support\Facades\DB;
use RuntimeException;

use function Lambdish\Phunctional\each;

final class MySqlLaravelDomainEventsConsumer
{
    public function __construct(private DomainEventMapping $eventMapping)
    {
    }

    public function consume(callable $subscribers, int $eventsToConsume): void
    {
        $events = DB::table('domain_events')->orderBy('occurred_at', 'asc')->limit($eventsToConsume)->get();

        each($this->executeSubscribers($subscribers), $events->toArray());

        $ids = $events->pluck('id');

        if (count($ids) > 0) {
            DB::table('domain_events')->whereIn('id', $ids)->delete();
        }
    }

    private function executeSubscribers(callable $subscribers): callable
    {
        return function (object $event) use ($subscribers): void {
            if (
                !isset(
                    $event->id,
                    $event->name,
                    $event->aggregate_id,
                    $event->body,
                    $event->occurred_at,
                )
            ) {
                return;
            }

            try {
                /** @var DomainEvent $domainEventClass */
                $domainEventClass = $this->eventMapping->for($event->name);
                $body             = Utils::jsonDecode($event->body);

                $domainEvent = $domainEventClass::fromPrimitives(
                    $event->aggregate_id,
                    $body,
                    $event->id,
                    $this->formatDate($event->occurred_at),
                );

                $subscribers($domainEvent);
            } catch (RuntimeException) {
                return;
            }
        };
    }

    private function formatDate(string $stringDate): string
    {
        return DateUtils::dateToString(new DateTimeImmutable($stringDate));
    }
}
