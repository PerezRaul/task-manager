<?php

declare(strict_types=1);

namespace Src\Shared\Application\HistoricalDomainEvents\RePublish;

use Src\Shared\Domain\Bus\Command\CommandHandler;
use Src\Shared\Domain\HistoricalDomainEvents\HistoricalDomainEventId;

final class RePublishHistoricalDomainEventCommandHandler implements CommandHandler
{
    public function __construct(private HistoricalDomainEventRePublish $rePublisher)
    {
    }

    public function __invoke(RePublishHistoricalDomainEventCommand $command): void
    {
        $this->rePublisher->__invoke(new HistoricalDomainEventId($command->id()));
    }
}
