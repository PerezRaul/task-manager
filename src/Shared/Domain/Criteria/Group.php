<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Criteria;

final class Group
{
    public function __construct(private GroupBy $groupBy)
    {
    }

    public static function fromValues(string $groupBy): Group
    {
        return new self(new GroupBy($groupBy));
    }

    public function groupBy(): GroupBy
    {
        return $this->groupBy;
    }

    public function serialize(): string
    {
        return sprintf('%s', $this->groupBy->value());
    }
}
