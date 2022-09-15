<?php

declare(strict_types=1);

namespace Src\Tasks\Application\Delete;

use Src\Shared\Domain\Bus\Command\Command;

final class DeleteTaskCommand implements Command
{
    public function __construct(private string $id)
    {
    }

    public function id(): string
    {
        return $this->id;
    }
}
