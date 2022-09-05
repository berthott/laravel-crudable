<?php

namespace berthott\Crudable\Tests\Feature\NamespaceConfig;

use berthott\Crudable\CrudableServiceProvider;
use berthott\Scopeable\ScopeableServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class NamespaceConfigStandardTest extends TestCase
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
        Config::set('crudable.namespace', __NAMESPACE__.'\User');
    }

    public function test_standard_option(): void
    {
        $expectedRoutes = [
            'users.index',
        ];
        $unexpectedRoutes = [
            'tags.index',
        ];

        $registeredRoutes = array_keys(Route::getRoutes()->getRoutesByName());
        foreach ($expectedRoutes as $route) {
            $this->assertContains($route, $registeredRoutes);
        }
        foreach ($unexpectedRoutes as $route) {
            $this->assertNotContains($route, $registeredRoutes);
        }
    }
}
