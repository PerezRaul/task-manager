<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Bus\Event;

use Src\Shared\Domain\Bus\Event\DomainEvent;
use RuntimeException;

final class DomainEventJsonSerializer
{
    public static function serialize(DomainEvent $domainEvent): string
    {
        $string = json_encode(
            [
                'data' => [
                    'id'          => $domainEvent->eventId(),
                    'type'        => $domainEvent::eventName(),
                    'occurred_at' => $domainEvent->occurredAt(),
                    'attributes'  => array_merge($domainEvent->toPrimitives(), [
                        'id'        => $domainEvent->aggregateId(),
                    ]),
                ],
                'meta' => [],
            ]
        );

        if (false === $string) {
            throw new RuntimeException('Unable to serialize domain event.');
        }

        return $string;
    }
}
