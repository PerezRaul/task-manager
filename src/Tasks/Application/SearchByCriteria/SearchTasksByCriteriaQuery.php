<?php

declare(strict_types=1);

namespace Src\Tasks\Application\SearchByCriteria;

use Src\Shared\Domain\Bus\Query\Query;

final class SearchTasksByCriteriaQuery implements Query
{
    public function __construct(
        private array $filters,
        private array $orders,
        private ?int $limit,
        private ?int $offset
    ) {
    }

    public function filters(): array
    {
        return $this->filters;
    }

    public function orders(): array
    {
        return $this->orders;
    }

    public function limit(): ?int
    {
        return $this->limit;
    }

    public function offset(): ?int
    {
        return $this->offset;
    }
}
