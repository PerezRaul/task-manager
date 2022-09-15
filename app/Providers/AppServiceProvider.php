<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadDatabaseMigrations();
    }

    /**
     * @return void
     */
    protected function loadDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations/mysql');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations/mongodb');
    }
}
