<?php

namespace berthott\Crudable\Tests\Feature\RouteOrder;

use berthott\Crudable\CrudableServiceProvider;
use berthott\Scopeable\ScopeableServiceProvider;
use Illuminate\Support\Facades\Config;
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
        Config::set('crudable.namespace', __NAMESPACE__);
    }
}
