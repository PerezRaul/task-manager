<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Bus\Event;

use Src\Shared\Domain\ArrUtils;
use Src\Shared\Domain\Bus\Event\DomainEventSubscriber;
use Src\Shared\Domain\Bus\Event\ShouldNotQueue;
use Src\Shared\Infrastructure\Bus\CallableFirstParameterExtractor;
use Traversable;

use function Lambdish\Phunctional\filter;

final class DomainEventSubscriberLocator
{
    private array $mappingArray;

    public function __construct(private Traversable $mapping)
    {
    }

    public function mappingArray(): array
    {
        return $this->mappingArray = $this->mappingArray ?? iterator_to_array($this->mapping);
    }

    public function allSubscribedTo(string $eventClass): array
    {
        $formatted = CallableFirstParameterExtractor::forPipedCallables($this->mappingArray());

        /** @var array $allSubscribedTo */
        $allSubscribedTo = ArrUtils::get($formatted, $eventClass, []);

        return $allSubscribedTo;
    }

    public function allShouldNotQueueSubscribedTo(string $eventClass): array
    {
        return filter(function (DomainEventSubscriber $subscriber) {
            return is_subclass_of($subscriber, ShouldNotQueue::class);
        }, $this->allSubscribedTo($eventClass));
    }

    public function allShouldQueue(): array
    {
        return filter(function (DomainEventSubscriber $subscriber) {
            return !is_subclass_of($subscriber, ShouldNotQueue::class);
        }, $this->mappingArray());
    }

    public function all(): array
    {
        return $this->mappingArray();
    }
}
