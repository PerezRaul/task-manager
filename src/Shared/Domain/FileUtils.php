<?php

declare(strict_types=1);

namespace Src\Shared\Domain;

use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

use function Lambdish\Phunctional\map;

final class FileUtils
{
    public static function classesThatImplements(string $interface, string ...$dirs): array
    {
        if (empty($dirs)) {
            /** @var string[] $dirs */
            $dirs = config('task-manager.bus.scan_dirs');
        }

        return array_values(map(
            function ($file) {
                return Utils::fullNamespace($file->getPathname());
            },
            with(new Finder())->in($dirs)->files()->name('*.php')->filter(function (SplFileInfo $file) use ($interface
            ) {
                $classNamespace = Utils::fullNamespace($file->getPathname());

                if (null === $classNamespace) {
                    return false;
                }

                $class = new ReflectionClass($classNamespace);

                return $class->implementsInterface($interface) && $classNamespace !== $interface;
            })
        ));
    }
}
