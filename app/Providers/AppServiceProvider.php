<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
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

    public function boot(): void
    {
        // Define o comprimento padrão de strings para 191 caracteres
        Schema::defaultStringLength(191);
    }
}
