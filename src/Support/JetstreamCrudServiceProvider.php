<?php

namespace Goodyweb\JetstreamCrud\Support;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class JetstreamCrudServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            \Goodyweb\JetstreamCrud\Console\GenerateCrudCommand::class,
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return [\Goodyweb\JetstreamCrud\Console\GenerateCrudCommand::class];
    }

}