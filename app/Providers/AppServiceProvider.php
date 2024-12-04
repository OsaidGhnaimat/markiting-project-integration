<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if ($this->app->environment('local')) {
            DB::listen(function ($query) {
                Log::debug($query->sql);
                Log::debug(serialize($query->bindings));
                Log::debug($query->time);
                Log::debug('--------------------');
            });
        }
    }
}
