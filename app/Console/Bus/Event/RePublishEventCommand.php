<?php

declare(strict_types=1);

namespace App\Console\Bus\Event;

use Illuminate\Console\Command;
use Src\Shared\Application\HistoricalDomainEvents\RePublish\RePublishHistoricalDomainEventCommand;
use Src\Shared\Domain\Bus\Command\CommandBus;

final class RePublishEventCommand extends Command
{
    protected $signature   = 'task-manager:domain-events:republish
                              {id : The id of the historical domain events}';
    protected $description = 'Republish event from historical domain events';

    public function handle(): void
    {
        /** @var CommandBus $commandBus */
        $commandBus = app(CommandBus::class);
        /** @var string $id */
        $id = $this->argument('id');

        $commandBus->dispatch(new RePublishHistoricalDomainEventCommand($id));

        $this->info(sprintf('Event %s has been republished', $id));
    }
}
