<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Bus\Command;

use Src\Shared\Domain\Bus\Command\Command;
use Src\Shared\Domain\Bus\Command\CommandBus;
use Src\Shared\Infrastructure\Bus\CallableFirstParameterExtractor;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

final class InMemorySymfonyCommandBus implements CommandBus
{
    private ?MessageBus $bus = null;

    public function __construct(private iterable $commandHandlers)
    {
    }

    public function dispatch(Command $command): void
    {
        try {
            $this->bus()->dispatch($command);
        } catch (NoHandlerForMessageException) {
            throw new CommandNotRegisteredError($command);
        } catch (HandlerFailedException $error) {
            throw $error->getPrevious() ?? $error;
        }
    }

    private function bus(): MessageBus
    {
        return $this->bus = $this->bus ?? new MessageBus(
                [
                    new HandleMessageMiddleware(
                        new HandlersLocator(CallableFirstParameterExtractor::forCallables($this->commandHandlers))
                    ),
                ]
            );
    }
}
