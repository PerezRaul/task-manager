<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Bus\Event;

use Src\Shared\Domain\Bus\Event\DomainEventSubscriber;
use RuntimeException;

use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\reindex;

final class DomainEventMapping
{
    private array $mapping;

    public function __construct(iterable $mapping)
    {
        /** @var array $mapping */
        $mapping = reduce($this->eventsExtractor(), $mapping, []);

        $this->mapping = $mapping;
    }

    public function for(string $name): mixed
    {
        if (!isset($this->mapping[$name])) {
            throw new RuntimeException("The Domain Event Class for <$name> doesn't exists or have no subscribers");
        }

        return $this->mapping[$name];
    }

    private function eventsExtractor(): callable
    {
        return fn(array $mapping, DomainEventSubscriber $subscriber) => array_merge(
            $mapping,
            reindex(
                $this->eventNameExtractor(),
                $subscriber::subscribedTo()
            )
        );
    }

    private function eventNameExtractor(): callable
    {
        return static fn(string $eventClass): string => $eventClass::eventName();
    }
}
