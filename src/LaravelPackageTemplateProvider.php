<?php


namespace Grosv\LaravelPackageTemplate;


use Illuminate\Support\ServiceProvider;

class LaravelPackageTemplateProvider extends ServiceProvider
{
    public function boot(): void
    {

    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-package-template');
    }
}