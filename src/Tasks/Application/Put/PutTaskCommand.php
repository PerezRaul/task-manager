<?php

declare(strict_types=1);

namespace Src\Tasks\Application\Put;

use Src\Shared\Domain\Bus\Command\Command;

final class PutTaskCommand implements Command
{
    public function __construct(
        private string $id,
        private string $title,
        private bool $isFinished,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function isFinished(): bool
    {
        return $this->isFinished;
    }
}
