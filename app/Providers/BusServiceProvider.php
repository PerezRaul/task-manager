<?php

declare(strict_types=1);

namespace App\Providers;

use App\Console\Bus\Event\EventsListCommand;
use App\Console\Bus\Event\RePublishEventCommand;
use Src\Shared\Domain\Bus\Command\CommandBus;
use Src\Shared\Domain\Bus\Command\CommandHandler;
use Src\Shared\Domain\Bus\Event\DomainEventSubscriber;
use Src\Shared\Domain\Bus\Event\EventBus;
use Src\Shared\Domain\Bus\Event\ShouldNotQueue;
use Src\Shared\Domain\Bus\Query\QueryBus;
use Src\Shared\Domain\Bus\Query\QueryHandler;
use Src\Shared\Domain\FileUtils;
use Src\Shared\Domain\StrUtils;
use Src\Shared\Infrastructure\Bus\Command\InMemorySymfonyCommandBus;
use Src\Shared\Infrastructure\Bus\Event\DomainEventMapping;
use Src\Shared\Infrastructure\Bus\Event\DomainEventSubscriberLocator;
use Src\Shared\Infrastructure\Bus\Event\InMemory\InMemorySymfonyEventBus;
use Src\Shared\Infrastructure\Bus\Event\Mysql\MysqlLaravelEventBus;
use Src\Shared\Infrastructure\Bus\Query\InMemorySymfonyQueryBus;
use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use RuntimeException;

use function Lambdish\Phunctional\filter;

final class BusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        /** @var Repository $config */
        $config = config();

        $this->mergeConfigFrom(__DIR__ . '/../../config/bus.php', 'task-manager.bus');

        if (app()->environment() === 'testing') {
            $config->set('task-manager.bus.scan_dirs', array_merge(
                (array) $config->get('task-manager.bus.scan_dirs'),
                [base_path('tests/**/*')],
            ));
        }

        if (StrUtils::contains(base_path(), 'testbench-core')) {
            $scanDirs = [base_path('../../../../src/**/*')];
            if (app()->environment() === 'testing') {
                $scanDirs[] = base_path('../../../../tests/**/*');
            }
            $config->set('task-manager.bus.scan_dirs', $scanDirs);
        }

        /** @var string[] $scanDirs */
        $scanDirs = $config->get('task-manager.bus.scan_dirs');

        $this->registerCommands();

        $this->registerQueryBus($scanDirs);
        $this->registerCommandBus($scanDirs);
        $this->registerEventBus($scanDirs);
    }

    private function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            if ($this->app->environment() === 'local') {
                $this->commands([
                    RePublishEventCommand::class,
                ]);
            }

            $this->commands([
                EventsListCommand::class,
            ]);
        }
    }

    private function registerQueryBus(array $scanDirs): void
    {
        $this->app->tag(
            FileUtils::classesThatImplements(QueryHandler::class, ...$scanDirs),
            'query_handler',
        );

        $this->app->singleton(QueryBus::class, function ($app) {
            return new InMemorySymfonyQueryBus($app->tagged('query_handler'));
        });
    }

    private function registerCommandBus(array $scanDirs): void
    {
        $this->app->tag(
            FileUtils::classesThatImplements(CommandHandler::class, ...$scanDirs),
            'command_handler',
        );

        $this->app->singleton(CommandBus::class, function ($app) {
            return new InMemorySymfonyCommandBus($app->tagged('command_handler'));
        });
    }

    private function registerEventBus(array $scanDirs): void
    {
        $subscribers = FileUtils::classesThatImplements(DomainEventSubscriber::class, ...$scanDirs);

        $this->app->tag($subscribers, 'domain_event_subscriber');

        $this->app->tag(filter(function (string $subscriber) {
            return is_subclass_of($subscriber, ShouldNotQueue::class);
        }, $subscribers), 'domain_event_should_not_queue_subscriber');

        $this->app->tag(filter(function (string $subscriber) {
            return !is_subclass_of($subscriber, ShouldNotQueue::class);
        }, $subscribers), 'domain_event_should_queue_subscriber');

        $this->app->singleton(DomainEventMapping::class, function ($app) {
            return new DomainEventMapping($app->tagged('domain_event_subscriber'));
        });

        $this->app->singleton(DomainEventSubscriberLocator::class, function ($app) {
            return new DomainEventSubscriberLocator($app->tagged('domain_event_subscriber'));
        });

        $connection    = strval(config('task-manager.bus.event.connection'));
        $configuration = config('task-manager.bus.event.connections.' . $connection);

        if (null === $configuration || !is_array($configuration)) {
            throw new RuntimeException(
                sprintf('No configuration found for event bus connection [%s]', $connection)
            );
        }

        switch ($configuration['driver']) {
            case 'memory':
                $this->registerEventBusForMemory();
                break;
            default:
                throw new InvalidArgumentException(
                    sprintf('Unsupported event bus connection driver [%s]', $configuration['driver'])
                );
        }
    }

    private function registerEventBusForMemory(): void
    {
        $this->app->singleton(EventBus::class, function ($app) {
            return new InMemorySymfonyEventBus(
                $app->tagged('domain_event_subscriber'),
                new MysqlLaravelEventBus($app->make(DomainEventSubscriberLocator::class)),
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/bus.php' => config_path('shared/bus.php'),
        ]);
    }
}
