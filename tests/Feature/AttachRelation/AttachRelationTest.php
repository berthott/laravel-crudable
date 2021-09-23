<?php

namespace berthott\Crudable\Tests\Feature\AttachRelation;

use Illuminate\Support\Facades\Route;

class AttachRelationTest extends TestCase
{
    public function test_user_routes_exist(): void
    {
        $expectedRoutes = [
            'users.index',
            'users.store',
            'users.show',
            'users.update',
            'users.destroy'
        ];
        $registeredRoutes = array_keys(Route::getRoutes()->getRoutesByName());
        foreach ($expectedRoutes as $route) {
            $this->assertContains($route, $registeredRoutes);
        }
    }

    public function test_relation_creation(): void
    {
        $role = Role::factory()->create();
        $this->assertModelExists($role);
        $userToStore = User::factory()->make();
        $body = array_merge(
            $userToStore->toArray(),
            ['roles' => [$role->id]],
        );
        $id = $this->post(route('users.store'), $body)
            ->assertStatus(201)
            ->assertJson(['name' => $userToStore->name])
            ->json()['id'];
        $this->assertDatabaseHas('users', [
            'id' => $id,
            'name' => $userToStore->name
        ]);
        $this->assertDatabaseHas('role_user', [
            'role_id' => $role->id,
            'user_id' => $id,
        ]);
    }
}
