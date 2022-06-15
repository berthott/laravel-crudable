<?php

namespace berthott\Crudable\Tests\Feature\NamespaceConfig;

use berthott\Crudable\CrudableServiceProvider;
use HaydenPierce\ClassFinder\ClassFinder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class NamespaceConfigRecursiveTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            CrudableServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        Config::set('crudable.namespace', __NAMESPACE__);
        Config::set('crudable.namespace_mode', ClassFinder::RECURSIVE_MODE);
    }

    public function test_recursive_option(): void
    {
        $expectedRoutes = [
            'users.index',
            'tags.index',
        ];
        $unexpectedRoutes = [
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
