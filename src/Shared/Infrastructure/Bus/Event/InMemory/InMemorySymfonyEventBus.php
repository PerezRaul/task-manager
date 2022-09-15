<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Bus\Event\InMemory;

use Src\Shared\Domain\Bus\Event\DomainEvent;
use Src\Shared\Domain\Bus\Event\EventBus;
use Src\Shared\Infrastructure\Bus\CallableFirstParameterExtractor;
use Src\Shared\Infrastructure\Bus\Event\Mysql\MysqlLaravelEventBus;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

class InMemorySymfonyEventBus implements EventBus
{
    private ?MessageBus $bus = null;

    public function __construct(private iterable $subscribers, private MysqlLaravelEventBus $mysqlPublisher)
    {
    }

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            try {
                $this->bus()->dispatch($event);
                $this->mysqlPublisher->historicalPublish($event);
            } catch (NoHandlerForMessageException) {
                continue;
            }
        }
    }

    private function bus(): MessageBus
    {
        return $this->bus = $this->bus ?? new MessageBus(
            [
                new HandleMessageMiddleware(
                    new HandlersLocator(
                        CallableFirstParameterExtractor::forPipedCallables($this->subscribers)
                    )
                ),
            ]
        );
    }
}
