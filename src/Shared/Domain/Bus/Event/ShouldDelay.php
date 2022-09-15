<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Bus\Event;

interface ShouldDelay
{
    /** The time in milliseconds to delay the subscriber */
    public static function delay(): int;
}
