<?php

namespace berthott\Crudable\Tests\Feature\RouteOptions;

use Illuminate\Support\Facades\Route;

class RouteOptionsTest extends TestCase
{
    public function test_user_routes(): void
    {
        $expectedRoutes = [
            'users.index',
        ];
        $unexpectedRoutes = [
            'users.store',
            'users.show',
            'users.update',
            'users.destroy',
        ];
        $registeredRoutes = array_keys(Route::getRoutes()->getRoutesByName());
        foreach ($expectedRoutes as $route) {
            $this->assertContains($route, $registeredRoutes);
        }
        foreach ($unexpectedRoutes as $route) {
            $this->assertNotContains($route, $registeredRoutes);
        }
    }

    public function test_tag_routes(): void
    {
        $expectedRoutes = [
            'tags.index',
            'tags.store',
            'tags.show',
            'tags.update',
        ];
        $unexpectedRoutes = [
            'tags.destroy',
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
