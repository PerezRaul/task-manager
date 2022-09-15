<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Bus\Query;

use Src\Shared\Domain\Bus\Query\Query;
use Src\Shared\Domain\Bus\Query\QueryBus;
use Src\Shared\Domain\Bus\Query\Response;
use Src\Shared\Infrastructure\Bus\CallableFirstParameterExtractor;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class InMemorySymfonyQueryBus implements QueryBus
{
    private ?MessageBus $bus = null;

    public function __construct(private iterable $queryHandlers)
    {
    }

    public function ask(Query $query): ?Response
    {
        try {
            /** @var HandledStamp $stamp */
            $stamp = $this->bus()->dispatch($query)->last(HandledStamp::class);

            /** @var Response|null $result */
            $result = $stamp->getResult();

            return $result;
        } catch (NoHandlerForMessageException) {
            throw new QueryNotRegisteredError($query);
        } catch (HandlerFailedException $error) {
            throw $error->getPrevious() ?? $error;
        }
    }

    private function bus(): MessageBus
    {
        return $this->bus = $this->bus ?? new MessageBus(
                [
                    new HandleMessageMiddleware(
                        new HandlersLocator(CallableFirstParameterExtractor::forCallables($this->queryHandlers))
                    ),
                ]
            );
    }
}
