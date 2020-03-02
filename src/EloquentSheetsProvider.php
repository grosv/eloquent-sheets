<?php

namespace Grosv\EloquentSheets;

use Illuminate\Support\ServiceProvider;

class EloquentSheetsProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'eloquent-sheets');
    }
}
