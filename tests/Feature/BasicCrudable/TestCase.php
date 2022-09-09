<?php

namespace berthott\Crudable\Tests\Feature\BasicCrudable;

use berthott\Crudable\CrudableServiceProvider;
use berthott\Scopeable\ScopeableServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            CrudableServiceProvider::class,
            ScopeableServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->setUpUserTable();
        Config::set('crudable.namespace', __NAMESPACE__);
    }

    private function setUpUserTable(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstname');
            $table->string('lastname');
            $table->integer('hours')->nullable();
            $table->timestamps();
        });
    }
}
