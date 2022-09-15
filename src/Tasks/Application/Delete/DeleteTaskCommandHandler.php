<?php

declare(strict_types=1);

namespace Src\Tasks\Application\Delete;

use Src\Shared\Domain\Bus\Command\CommandHandler;
use Src\Shared\Domain\Tasks\TaskId;

final class DeleteTaskCommandHandler implements CommandHandler
{
    public function __construct(private TaskDelete $deleter)
    {
    }

    public function __invoke(DeleteTaskCommand $command): void
    {
        $this->deleter->__invoke(new TaskId($command->id()));
    }
}
