<?php

declare(strict_types=1);

namespace Src\Shared\Application\HistoricalDomainEvents\RePublish;

use Src\Shared\Domain\Bus\Command\Command;

final class RePublishHistoricalDomainEventCommand implements Command
{
    public function __construct(private string $id)
    {
    }

    public function id(): string
    {
        return $this->id;
    }
}
