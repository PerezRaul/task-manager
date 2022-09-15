<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Src\Shared\Domain\Bus\Command\CommandBus;
use Src\Tasks\Application\Put\PutTaskCommand;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $tasks = require __DIR__ . '/tasks/tasks.php';

        foreach ($tasks as $task) {
            app(CommandBus::class)->dispatch(new PutTaskCommand(
                $task['id'],
                $task['title'],
                $task['is_finished'],
            ));
        }
    }
}
