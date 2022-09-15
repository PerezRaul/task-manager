<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Src\Shared\Domain\ArrUtils;
use Src\Shared\Domain\Bus\Query\QueryBus;
use Src\Shared\Domain\Exceptions\NotExists;
use Src\Shared\Domain\StrUtils;

use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\filter;

trait PermissionableRequestWithDefaultTrait
{
    use PermissionableRequestTrait;

    abstract protected function permissionableRouteParameterName(): string;

    abstract protected function permissionableFindQueryClass(): string;

    public function addDefaultsToRequest(): void
    {
        $entityId = $this->route($this->permissionableRouteParameterName(), null);
        if (null === $entityId) {
            return;
        }

        $class = $this->permissionableFindQueryClass();

        $query = new $class($entityId);

        try {
            /** @phpstan-ignore-next-line */
            $entity = app(QueryBus::class)->ask($query);
        } catch (NotExists) {
            return;
        }

        $keys = filter(function (string $rule) {
            return !ArrUtils::hasValue(explode('.', $rule), '*');
        }, array_keys($this->rules()));

        each(function (string $key) use ($entity) {
            if ($this->has($key)) {
                return;
            }

            $method = StrUtils::camel($key);

            $this->offsetSet($key, $entity->$method());
        }, $keys);
    }
}
