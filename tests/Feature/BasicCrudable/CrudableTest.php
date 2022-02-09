<?php

namespace berthott\Crudable\Tests\Feature\BasicCrudable;

use Illuminate\Support\Facades\Route;

class CrudableTest extends TestCase
{
    public function test_user_routes_exist(): void
    {
        $expectedRoutes = [
            'users.index',
            'users.store',
            'users.show',
            'users.update',
            'users.destroy',

            'users.schema',
        ];
        $registeredRoutes = array_keys(Route::getRoutes()->getRoutesByName());
        foreach ($expectedRoutes as $route) {
            $this->assertContains($route, $registeredRoutes);
        }
    }

    public function test_user_index(): void
    {
        $users = User::factory()->count(3)->create();
        $this->get(route('users.index'))
            ->assertStatus(200)
            ->assertSimilarJson($users->toArray());
    }

    public function test_show_user(): void
    {
        $user = User::factory()->create();
        $this->get(route('users.show', ['user' => $user->id]))
            ->assertStatus(200)
            ->assertJsonFragment($user->toArray());
    }

    public function test_store_user(): void
    {
        $userToStore = User::factory()->make();
        $id = $this->post(route('users.store'), $userToStore->toArray())
            ->assertStatus(201)
            ->assertJson(['firstname' => $userToStore->firstname])
            ->json()['id'];
        $this->assertDatabaseHas('users', [
            'id' => $id,
            'firstname' => $userToStore->firstname
        ]);
    }

    public function test_store_user_validation(): void
    {
        $userToStore = User::make(['lastname' => 'Test']);
        $this->post(route('users.store'), $userToStore->toArray())
            ->assertStatus(422)
            ->assertJsonValidationErrors('firstname');
        $this->assertDatabaseMissing('users', [
            'lastname' => $userToStore->lastname
        ]);
    }

    public function test_schema_route(): void
    {
        $this->get(route('users.schema'))
            ->assertStatus(200)
            ->assertJsonFragment(['column' => 'id', 'type' => 'integer'])
            ->assertJsonFragment(['column' => 'firstname', 'type' => 'string'])
            ->assertJsonFragment(['column' => 'lastname', 'type' => 'string'])
            ->assertJsonFragment(['column' => 'created_at', 'type' => 'datetime'])
            ->assertJsonFragment(['column' => 'updated_at', 'type' => 'datetime'])
            ->assertJsonFragment(['column' => 'test', 'type' => 'appends']);
    }

    public function test_update_user(): void
    {
        $user = User::factory()->create();
        $change = ['firstname' => 'Test'];
        $this->put(route('users.update', ['user' => $user->id]), $change)
            ->assertStatus(200)
            ->assertJson($change);
        $this->assertDatabaseHas('users', array_merge(
            [
                'id' => $user->id,
                'lastname' => $user->lastname
            ],
            $change
        ));
    }

    public function test_delete_user(): void
    {
        $user = User::factory()->create();
        $this->assertModelExists($user);
        $this->delete(route('users.destroy', ['user' => $user->id]))
            ->assertStatus(200);
        $this->assertModelMissing($user);
    }

    public function test_delete_many_users(): void
    {
        $users = User::factory()->count(10)->create();
        foreach ($users as $user) {
            $this->assertModelExists($user);
        }
        $usersToDelete = $users->slice(0, 5);
        $usersToPersist = $users->slice(5, 5);
        $this->delete(route('users.destroy_many'), ['ids' => $usersToDelete->pluck('id')->toArray()])
            ->assertStatus(200);
        foreach ($usersToDelete as $user) {
            $this->assertModelMissing($user);
        }
        foreach ($usersToPersist as $user) {
            $this->assertModelExists($user);
        }
        $this->delete(route('users.destroy_many'), ['ids' => [11, 12]])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['ids.0', 'ids.1']);
    }
}
