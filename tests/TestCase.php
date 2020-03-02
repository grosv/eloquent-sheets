<?php

namespace Tests;

use Grosv\EloquentSheets\EloquentSheetsProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Revolution\Google\Sheets\Providers\SheetsServiceProvider;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [EloquentSheetsProvider::class, SheetsServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('app.key', 'base64:r0w0xC+mYYqjbZhHZ3uk1oH63VadA3RKrMW52OlIDzI=');
        $app['config']->set('google.application_name', 'laravel-package');
        // Paste any valid Google Sheets API key (server key) into ../credentials/key.txt to access the test spreadsheet
        // The credentials directory is not tracked in the git repository
        $app['config']->set('google.developer_key', file_get_contents(__DIR__.'/../credentials/key.txt'));
    }
}
