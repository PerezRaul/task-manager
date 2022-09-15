<?php

declare(strict_types=1);

namespace App\Providers;

use Src\Shared\Domain\ArrUtils;
use Src\Shared\Domain\HistoricalDomainEvents\HistoricalDomainEventRepository;
use Src\Shared\Infrastructure\Persistence\HistoricalDomainEvents\EloquentHistoricalDomainEventRepository;
use Illuminate\Container\ContextualBindingBuilder;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

use function Lambdish\Phunctional\each;

final class IocServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/shared/ioc.php', 'task-manager.ioc');

        $this->binds();
        $this->singletons();

    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/shared/ioc.php' => config_path('shared/ioc.php'),
        ]);
    }

    private function binds(): void
    {
        each(function ($concrete, $abstract) {
            is_array($concrete) ?
                $this->contextualBinding($abstract, $concrete) :
                $this->app->bind($abstract, function (Application $app) use ($concrete) {
                    return $app->make($concrete);
                });
        }, array_merge([
            HistoricalDomainEventRepository::class => EloquentHistoricalDomainEventRepository::class,
        ], (array) config('task-manager.ioc.binds')));
    }

    private function singletons(): void
    {
        each(function ($concrete, $abstract) {
            $this->app->singleton($abstract, function (Application $app) use ($concrete) {
                return $app->make($concrete);
            });
        }, (array) config('task-manager.ioc.singletons'));
    }

    private function contextualBinding(string $abstract, array $concrete): void
    {
        if (!ArrUtils::has($concrete, 'neeeds')) {
            throw new InvalidArgumentException(
                sprintf('Missing key [neeeds] for contextual binding %s', $abstract)
            );
        }

        if (!ArrUtils::has($concrete, 'concrete')) {
            throw new InvalidArgumentException(
                sprintf('Missing key [concrete] for contextual binding %s', $abstract)
            );
        }

        switch (ArrUtils::get($concrete, 'type')) {
            case 'tagged':
                $this->app->when($abstract)->needs($concrete['needs'])->giveTagged($concrete['concrete']);
                break;
            case 'config':
                /** @var ContextualBindingBuilder $contextualBindingBuilder */
                $contextualBindingBuilder = $this->app->when($abstract)->needs($concrete['needs']);

                $contextualBindingBuilder->giveConfig($concrete['concrete']);
                break;
            case null:
                $this->app->when($abstract)->needs($concrete['needs'])->give($concrete['concrete']);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Invalid [type] for contextual binding %s', $abstract));
        }
    }
}
