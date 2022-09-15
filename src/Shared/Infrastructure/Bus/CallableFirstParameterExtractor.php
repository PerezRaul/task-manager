<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Bus;

use Src\Shared\Domain\Bus\Event\DomainEventSubscriber;
use NunoMaduro\Larastan\Properties\ReflectionTypeContainer;
use ReflectionClass;
use ReflectionMethod;

use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\reduce;
use function Lambdish\Phunctional\reindex;

final class CallableFirstParameterExtractor
{
    public static function forCallables(iterable $callables): array
    {
        return map(self::unflatten(), reindex(self::classExtractor(new self()), $callables));
    }

    public static function forPipedCallables(iterable $callables): array
    {
        /** @var array $forPipedCallables */
        $forPipedCallables = reduce(self::pipedCallablesReducer(), $callables, []);

        return $forPipedCallables;
    }

    private static function classExtractor(CallableFirstParameterExtractor $parameterExtractor): callable
    {
        return static function (string|object $handler) use ($parameterExtractor): ?string {
            /** @var class-string|object $handler */
            return $parameterExtractor->extract($handler);
        };
    }

    private static function pipedCallablesReducer(): callable
    {
        return static function ($subscribers, DomainEventSubscriber $subscriber): array {
            $subscribedEvents = $subscriber::subscribedTo();

            foreach ($subscribedEvents as $subscribedEvent) {
                $subscribers[$subscribedEvent][] = $subscriber;
            }

            return $subscribers;
        };
    }

    private static function unflatten(): callable
    {
        return static fn($value) => [$value];
    }

    /**
     * @psalm-param class-string|object $class
     */
    public function extract(string|object $class): ?string
    {
        $reflector = new ReflectionClass($class);
        $method    = $reflector->getMethod('__invoke');

        if ($this->hasOnlyOneParameter($method)) {
            return $this->firstParameterClassFrom($method);
        }

        return null;
    }

    private function firstParameterClassFrom(ReflectionMethod $method): string
    {
        /** @var ReflectionTypeContainer $reflectionType */
        $reflectionType = $method->getParameters()[0]->getType();

        return $reflectionType->getName();
    }

    private function hasOnlyOneParameter(ReflectionMethod $method): bool
    {
        return $method->getNumberOfParameters() === 1;
    }
}
