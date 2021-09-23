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
        $id = $this->post(route('users.store'), array_merge(
            $userToStore->toArray(),
            ['roles' => [$role->id]],
        ))
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

    public function test_relation_update(): void
    {
        $roles = Role::factory()->count(2)->create();
        $user = User::factory()->create();
        $user->roles()->attach($roles[0]);
        $this->assertModelExists($roles[0]);
        $this->assertModelExists($user);
        $this->assertDatabaseHas('role_user', [
            'role_id' => $roles[0]->id,
            'user_id' => $user->id,
        ]);
        $this->put(route('users.update', ['user' => $user->id]), ['roles' => [$roles[1]->id]])
            ->assertStatus(200);
        $this->assertDatabaseMissing('role_user', [
            'role_id' => $roles[0]->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('role_user', [
            'role_id' => $roles[1]->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_relation_delete(): void
    {
        $role = Role::factory()->create();
        $user = User::factory()->create();
        $user->roles()->attach($role);
        $this->assertModelExists($role);
        $this->assertModelExists($user);
        $this->assertDatabaseHas('role_user', [
            'role_id' => $role->id,
            'user_id' => $user->id,
        ]);
        $this->delete(route('users.destroy', ['user' => $user->id]))
            ->assertStatus(200);
        $this->assertDatabaseMissing('role_user', [
            'role_id' => $role->id,
            'user_id' => $user->id,
        ]);
    }
}
