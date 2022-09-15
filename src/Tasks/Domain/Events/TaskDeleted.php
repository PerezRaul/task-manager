<?php

declare(strict_types=1);

namespace Src\Tasks\Domain\Events;

use Src\Shared\Domain\Audits\Events\Auditable;
use Src\Shared\Domain\Bus\Event\DomainEvent;

final class TaskDeleted extends DomainEvent implements Auditable
{
    public function __construct(
        string $id,
        private string $title,
        private bool $isFinished,
        private string $createdAt,
        private string $updatedAt,
        string $eventId = null,
        string $occurredAt = null,
    ) {
        parent::__construct($id, $eventId, $occurredAt);
    }

    public static function eventName(): string
    {
        return 'task-manager.1.event.task.deleted';
    }

    public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredAt,
    ): DomainEvent {
        return new self(
            $aggregateId,
            $body['title'],
            $body['is_finished'],
            $body['created_at'],
            $body['updated_at'],
            $eventId,
            $occurredAt,
        );
    }

    public function toPrimitives(): array
    {
        return [
            'title'       => $this->title,
            'is_finished' => $this->isFinished,
            'created_at'  => $this->createdAt,
            'updated_at'  => $this->updatedAt,
        ];
    }

    public function title(): string
    {
        return $this->title;
    }

    public function isFinished(): bool
    {
        return $this->isFinished;
    }

    public function createdAt(): string
    {
        return $this->createdAt;
    }

    public function updatedAt(): string
    {
        return $this->updatedAt;
    }
}
