<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Audits\Events;

use Src\Shared\Domain\Bus\Event\DomainEvent;

interface Auditable
{
    public function aggregateId(): string;

    public static function eventName(): string;

    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredAt
    ): DomainEvent;

    public function toPrimitives(): array;

    public function occurredAt(): string;
}
