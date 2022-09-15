<?php

declare(strict_types=1);

namespace Src\Tasks\Application\Put;

use Src\Shared\Domain\Bus\Command\CommandHandler;
use Src\Shared\Domain\Tasks\TaskId;
use Src\Tasks\Domain\TaskIsFinished;
use Src\Tasks\Domain\TaskTitle;

final class PutTaskCommandHandler implements CommandHandler
{
    public function __construct(private TaskPut $putter)
    {
    }

    public function __invoke(PutTaskCommand $command): void
    {
        $id         = new TaskId($command->id());
        $title      = new TaskTitle($command->title());
        $isFinished = new TaskIsFinished($command->isFinished());

        $this->putter->__invoke(
            $id,
            $title,
            $isFinished,
        );
    }
}
