<?php

use Src\Shared\Domain\ValueObject\Uuid;

return [
    [
        'id'          => Uuid::random()->value(),
        'title'       => 'Develop structure',
        'is_finished' => false,
    ],
    [
        'id'          => Uuid::random()->value(),
        'title'       => 'Team Meeting',
        'is_finished' => true,
    ],
    [
        'id'          => Uuid::random()->value(),
        'title'       => 'Develop controllers',
        'is_finished' => false,
    ],
];
