<?php

namespace berthott\Crudable\Tests\Feature\RouteOrder;

use Illuminate\Support\Facades\Route;

class RouteOrderTest extends TestCase
{
    public function test_routes_order_success(): void
    {
        $expectedRouteOrder = [
            'users.before',
            'users.schema',
            'users.destroy_many',
            'users.index',
            'users.store',
            'users.show',
            'users.update',
            'users.destroy',
            'users.after',
        ];
        $registeredRoutes = array_keys(Route::getRoutes()->getRoutesByName());
        $this->assertEquals($expectedRouteOrder, $registeredRoutes);
    }

    public function test_routes_order_fail(): void
    {
        $expectedRouteOrder = [
            'users.after',
            'users.schema',
            'users.destroy_many',
            'users.index',
            'users.store',
            'users.show',
            'users.update',
            'users.destroy',
            'users.before',
        ];
        $registeredRoutes = array_keys(Route::getRoutes()->getRoutesByName());
        $this->assertNotEquals($expectedRouteOrder, $registeredRoutes);
    }
}
