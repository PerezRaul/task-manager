<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Persistence\HistoricalDomainEvents;

use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\HistoricalDomainEvents\HistoricalDomainEvent;
use Src\Shared\Domain\HistoricalDomainEvents\HistoricalDomainEventId;
use Src\Shared\Domain\HistoricalDomainEvents\HistoricalDomainEventRepository;
use Src\Shared\Domain\HistoricalDomainEvents\HistoricalDomainEvents;
use Src\Shared\Infrastructure\Persistence\Eloquent\EloquentCriteriaConverter;
use Src\Shared\Infrastructure\Persistence\Eloquent\EloquentRepository;
use Src\Shared\Infrastructure\Persistence\HistoricalDomainEvents\Eloquent\HistoricalDomainEvent as EloquentHistoricalDomainEvent;

use function Lambdish\Phunctional\map;

final class EloquentHistoricalDomainEventRepository extends EloquentRepository implements HistoricalDomainEventRepository
{
    public function search(HistoricalDomainEventId $id): ?HistoricalDomainEvent
    {
        $model = EloquentHistoricalDomainEvent::find($id->value());

        if (null === $model) {
            return null;
        }

        return $this->transformModelToDomainEntity($model);
    }

    public function matching(Criteria $criteria): HistoricalDomainEvents
    {
        $query = EloquentHistoricalDomainEvent::query();

        EloquentCriteriaConverter::apply($query, $criteria);

        return new HistoricalDomainEvents(map(function (EloquentHistoricalDomainEvent $model) {
            return $this->transformModelToDomainEntity($model);
        }, $query->get()->all()));
    }

    public function matchingCount(Criteria $criteria): int
    {
        $query = EloquentHistoricalDomainEvent::query();

        EloquentCriteriaConverter::apply($query, $criteria);

        return $query->count('id');
    }

    private function transformModelToDomainEntity(EloquentHistoricalDomainEvent $model): HistoricalDomainEvent
    {
        return HistoricalDomainEvent::fromPrimitives((array) $model->getOriginal());
    }
}
