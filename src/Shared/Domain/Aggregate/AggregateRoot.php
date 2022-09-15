<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Aggregate;

use DateTimeInterface;
use Src\Shared\Domain\ArrUtils;
use Src\Shared\Domain\Bus\Event\DomainEvent;
use Src\Shared\Domain\DateUtils;
use Src\Shared\Domain\StrUtils;
use Src\Shared\Domain\ValueObject\ArrayValueObject;
use Src\Shared\Domain\ValueObject\I18nValueObject;
use InvalidArgumentException;
use ReflectionClass;

use function Lambdish\Phunctional\filter;

abstract class AggregateRoot
{
    protected bool $wasRecentlyCreated = false;
    private array  $changes            = [];
    private array  $domainEvents       = [];

    final public function pullDomainEvents(): array
    {
        $domainEvents       = $this->domainEvents;
        $this->domainEvents = [];

        return $domainEvents;
    }

    final protected function record(DomainEvent $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }

    final protected function recordOnChanges(DomainEvent $domainEvent): void
    {
        if (!$this->hasChanges()) {
            return;
        }

        $this->record($domainEvent);
    }

    final protected function applyChanges(mixed ...$data): void
    {
        $changes = $this->getChanges($data);

        if (count($changes) === 1 && ArrUtils::has($changes, 'updatedAt')) {
            return;
        }

        $this->changes = ArrUtils::mapWithKeys(function ($value) {
            $key = StrUtils::camel(
                StrUtils::replaceFirst((new ReflectionClass($this))->getShortName(), '',
                    (new ReflectionClass($value))->getShortName())
            );

            $oldData = [
                StrUtils::snake($key) => $this->{$key}->value() instanceof DateTimeInterface ?
                    $this->{$key}->__toString() :
                    $this->{$key}->value()
            ];

            $this->{$key} = $value;

            return $oldData;
        }, $changes);
    }

    final public function changes(): array
    {
        return $this->changes;
    }

    final public function hasChanges(): bool
    {
        return true === $this->wasRecentlyCreated || !empty($this->changes);
    }

    private function getChanges(array $data): array
    {
        $filtered = ArrUtils::keyBy(
            $data,
            fn($value) => StrUtils::camel(
                StrUtils::replaceFirst(
                    (new ReflectionClass($this))->getShortName(),
                    '',
                    (new ReflectionClass($value))->getShortName()
                )
            )
        );

        $filtered = filter(function ($value, $key) {
            if (!isset($this->{$key})) {
                throw new InvalidArgumentException(sprintf('Property <%s> does not exist.', $key));
            }

            if ($this->{$key}->value() instanceof DateTimeInterface || $value->value() instanceof DateTimeInterface) {
                $new    = $value->value() ? DateUtils::dateToString($value->value()) : null;
                $actual = $this->{$key}->value() ? DateUtils::dateToString($this->{$key}->value()) : null;

                return $new !== $actual;
            } elseif ($this->{$key} instanceof ArrayValueObject || $this->{$key} instanceof I18nValueObject) {
                return json_encode($value->value()) !== json_encode($this->{$key}->value());
            }

            return $value->value() !== $this->{$key}->value();
        }, $filtered);

        return $filtered;
    }
}
