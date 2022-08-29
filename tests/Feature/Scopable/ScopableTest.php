<?php

namespace berthott\Crudable\Tests\Feature\Scopable;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

class ScopableTest extends TestCase
{
    public function test_routes_exist(): void
    {
        $expectedRoutes = [
            'users.index',
            'entity_ones.index',
            'entity_manies.index',
            'scopable_ones.index',
            'scopable_manies.index',
        ];
        $registeredRoutes = array_keys(Route::getRoutes()->getRoutesByName());
        foreach ($expectedRoutes as $route) {
            $this->assertContains($route, $registeredRoutes);
        }
    }

    public function test_scopable_index_one_to_many(): void
    {
        $scropableAllowed = ScopableOne::factory()->create();
        $scropableNotAllowed = ScopableOne::factory()->create();
        $user = User::factory()->for($scropableAllowed)->create();
        $entityToInclude = EntityOne::factory()->for($scropableAllowed)->create();
        $entityNotToInclude = EntityOne::factory()->for($scropableNotAllowed)->create();
        
        $this->actingAs($user);

        $this->get(route('entity_ones.index'))
            ->assertStatus(200)
            ->assertJsonFragment($this->entryArray($entityToInclude))
            ->assertJsonMissing($this->entryArray($entityNotToInclude));
    }

    public function test_scopable_index_many_to_many(): void
    {
        $scropableAllowed = ScopableMany::factory()->create();
        $scropableNotAllowed = ScopableMany::factory()->create();
        $user = User::factory()->hasAttached($scropableAllowed)->create();
        $entityToInclude = EntityMany::factory()->hasAttached($scropableAllowed)->create();
        $entityNotToInclude = EntityMany::factory()->hasAttached($scropableNotAllowed)->create();
        
        $this->actingAs($user);

        $this->get(route('entity_manies.index'))
            ->assertStatus(200)
            ->assertJsonFragment($this->entryArray($entityToInclude))
            ->assertJsonMissing($this->entryArray($entityNotToInclude));
    }

    private function entryArray($model): array 
    {
        return Arr::except($model->toArray(), ['created_at', 'updated_at']);
    }
}
