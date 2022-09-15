<?php

declare(strict_types=1);

namespace Src\Tasks\Application\Find;

use Src\Shared\Domain\Bus\Query\Query;

final class FindTaskQuery implements Query
{
    public function __construct(private string $id)
    {
    }

    public function id(): string
    {
        return $this->id;
    }
}
