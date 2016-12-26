<?php

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://registry.dev';
    protected static $setupDatabase = false;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }
    public function setUp()
    {
        parent::setUp();
        if(self::$setupDatabase)
        {
            $this->setupDatabase();
        }
    }
    protected function setupDatabase()
    {
        Artisan::call('migrate:refresh');
        Artisan::call('db:seed');
        self::$setupDatabase = false;
    }
}
