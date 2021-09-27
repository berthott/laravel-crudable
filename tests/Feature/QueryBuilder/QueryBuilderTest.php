<?php

namespace berthott\Crudable\Tests\Feature\QueryBuilder;

use Illuminate\Support\Facades\Route;

class QueryBuilderTest extends TestCase
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

    public function test_filter_success(): void
    {
        User::factory(['firstname' => 'Jan'])->create();
        User::factory(['firstname' => 'Jana'])->create();
        $this->get(route('users.index', [
            'filter' => [
                'firstname' => 'Jan'
            ]
        ]))
            ->assertStatus(200)
            ->assertJsonFragment(['firstname' => 'Jan'])
            ->assertJsonFragment(['firstname' => 'Jana']);
        $this->get(route('users.index', [
            'filter' => [
                'firstname' => 'Jana'
            ]
        ]))
            ->assertStatus(200)
            ->assertJsonMissing(['firstname' => 'Jan'])
            ->assertJsonFragment(['firstname' => 'Jana']);
    }

    public function test_filter_fail(): void
    {
        $this->get(route('users.index', [
            'filter' => [
                'lastname' => 'Jan'
            ]
        ]))
            ->assertStatus(400);
    }

    public function test_sort_success(): void
    {
        User::factory()->count(2)->create();
        $json = $this->get(route('users.index', [
            'sort' => 'lastname'
        ]))
            ->assertStatus(200)
            ->json();
        $this->get(route('users.index', [
            'sort' => '-lastname'
        ]))
            ->assertStatus(200)
            ->assertExactJson(array_reverse($json));
    }

    public function test_sort_fail(): void
    {
        $this->get(route('users.index', [
            'sort' => 'firstname'
        ]))
            ->assertStatus(400);
    }

    public function test_fields_success(): void
    {
        $user = User::factory()->create();
        $this->get(route('users.index', [
            'fields' => [
                'users' => 'firstname'
            ]
        ]))
            ->assertStatus(200)
            ->assertJsonMissing(['lastname' => $user->lastname])
            ->assertJsonFragment(['firstname' => $user->firstname]);
    }

    public function test_fields_fail(): void
    {
        $this->get(route('users.index', [
            'fields' => 'wrong'
        ]))
            ->assertStatus(400);
    }


    public function test_include_success(): void
    {
        User::factory()->create();
        $this->get(route('users.index'))
            ->assertStatus(200)
            ->assertJsonMissing(['roles' => []]);
        $this->get(route('users.index', [
            'include' => 'roles'
        ]))
            ->assertStatus(200)
            ->assertJsonFragment(['roles' => []]);
    }

    public function test_include_fail(): void
    {
        $this->get(route('users.index', [
            'include' => 'wrong'
        ]))
            ->assertStatus(400);
    }

    public function test_append_success(): void
    {
        $user = User::factory()->create();
        $this->get(route('users.index'))
            ->assertStatus(200)
            ->assertJsonMissing(['fullname' => "$user->firstname $user->lastname"]);
        $this->get(route('users.index', [
            'append' => 'fullname'
        ]))
            ->assertStatus(200)
            ->assertJsonFragment(['fullname' => "$user->firstname $user->lastname"]);
    }

    public function test_append_fail(): void
    {
        $this->get(route('users.index', [
            'append' => 'wrong'
        ]))
            ->assertStatus(400);
    }
}
