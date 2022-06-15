<?php

namespace berthott\Crudable\Tests\Feature\Middlewares;

use Illuminate\Support\Facades\Route;

class MiddlewaresTest extends TestCase
{
    public function test_middlewares(): void
    {
        foreach (Route::getRoutes()->getRoutesByName() as $routeName => $route) {
            $this->assertContains('api', $route->middleware());
            if (str_starts_with($routeName, 'users')) {
                $this->assertContains('test_middleware', $route->middleware());
            } else {
                $this->assertNotContains('test_middleware', $route->middleware());
            } 
        }
    }
}
