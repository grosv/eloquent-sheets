<?php

namespace Grosv\EloquentSheets;

use Grosv\EloquentSheets\Commands\MakeSheetModelCommand;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class EloquentSheetsProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->commands([
            MakeSheetModelCommand::class,
        ]);
        Config::set('google-sheets.app_path', app_path());
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'eloquent-sheets');
    }
}
