<?php

declare(strict_types=1);

namespace App\Console\Bus\Event;

use Illuminate\Console\Command;
use Src\Shared\Domain\Bus\Event\DomainEventSubscriber;
use Src\Shared\Domain\StrUtils;

use function Lambdish\Phunctional\each;

final class EventsListCommand extends Command
{
    protected $signature   = 'task-manager:domain-events:list
                              {--search=* : Will return events / subscribers that match with the string}';
    protected $description = 'List all events';

    public function handle(): void
    {
        $domainEvents = $this->domainEvents();

        foreach ($domainEvents as $domainEvent) {
            $this->info($domainEvent['event']);
            foreach ($domainEvent['listeners'] as $listener) {
                $this->line($listener);
            }
            $this->newLine();
        }
    }

    private function domainEvents(): array
    {
        $search = (array) $this->option('search');

        $domainEvents = [];

        each(function (DomainEventSubscriber $subscriber) use ($search, &$domainEvents) {
            $subscriberClass = get_class($subscriber);
            foreach ($subscriber::subscribedTo() as $domainEvent) {
                if (
                    !empty($search) &&
                    !(StrUtils::contains($domainEvent, ...$search) || StrUtils::contains($subscriberClass, ...$search))
                ) {
                    continue;
                }

                if (!isset($domainEvents[$domainEvent])) {
                    $domainEvents[$domainEvent] = [
                        'event'     => $domainEvent,
                        'listeners' => [],
                    ];
                }
                $domainEvents[$domainEvent]['listeners'][] = $subscriberClass;
            }
        }, app()->tagged('domain_event_subscriber'));

        return array_values($domainEvents);
    }
}
