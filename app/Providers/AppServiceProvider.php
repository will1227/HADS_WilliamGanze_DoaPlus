<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // RNF07: em produção todos os links e redirecionamentos saem em HTTPS
        // (o service worker e as notificações push também só funcionam com HTTPS)
        URL::forceHttps($this->app->isProduction());
    }
}
