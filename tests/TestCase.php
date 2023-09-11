<?php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    private function setUpDatabase(): void
    {
        $schema = $this->app['db']->connection()->getSchemaBuilder();

        $schema->create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid();
        });

        $schema->create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->uuid();
        });
    }
}
