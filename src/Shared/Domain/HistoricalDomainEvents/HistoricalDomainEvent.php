<?php

declare(strict_types=1);

namespace Src\Shared\Domain\HistoricalDomainEvents;

use Src\Shared\Domain\Aggregate\AggregateRoot;

final class HistoricalDomainEvent extends AggregateRoot
{
    public function __construct(
        protected HistoricalDomainEventId $id,
        protected HistoricalDomainEventAggregateId $aggregateId,
        protected HistoricalDomainEventName $name,
        protected HistoricalDomainEventBody $body,
        protected HistoricalDomainEventOccurredAt $occurredAt,
    ) {
    }

    public static function fromPrimitives(array $primitives): self
    {
        return new self(
            new HistoricalDomainEventId($primitives['id']),
            new HistoricalDomainEventAggregateId($primitives['aggregate_id']),
            new HistoricalDomainEventName($primitives['name']),
            new HistoricalDomainEventBody($primitives['body']),
            new HistoricalDomainEventOccurredAt($primitives['occurred_at']),
        );
    }

    public function toPrimitives(): array
    {
        return [
            'id'           => $this->id->value(),
            'aggregate_id' => $this->aggregateId->value(),
            'name'         => $this->name->value(),
            'body'         => $this->body->value(),
            'occurred_at'  => $this->occurredAt->__toString(),
        ];
    }

    public function id(): HistoricalDomainEventId
    {
        return $this->id;
    }

    public function aggregateId(): HistoricalDomainEventAggregateId
    {
        return $this->aggregateId;
    }

    public function name(): HistoricalDomainEventName
    {
        return $this->name;
    }

    public function body(): HistoricalDomainEventBody
    {
        return $this->body;
    }

    public function occurredAt(): HistoricalDomainEventOccurredAt
    {
        return $this->occurredAt;
    }
}
