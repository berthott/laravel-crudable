<?php

namespace berthott\Crudable\Tests\Feature\Scopable;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class ScopableTest extends TestCase
{
    private function entryArray($model): array 
    {
        return Arr::except($model->toArray(), ['created_at', 'updated_at']);
    }

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
        $user = User::factory()->for($scropableAllowed, 'scopable_one')->create();
        $entityToInclude = EntityOne::factory()->for($scropableAllowed, 'scopable_one')->create();
        $entityNotToInclude = EntityOne::factory()->for($scropableNotAllowed, 'scopable_one')->create();
        
        $this->actingAs($user);

        $this->get(route('entity_ones.index'))
            ->assertSuccessful()
            ->assertJsonFragment($this->entryArray($entityToInclude))
            ->assertJsonMissing($this->entryArray($entityNotToInclude));
    }

    public function test_scopable_index_many_to_many(): void
    {
        $scropableAllowed = ScopableMany::factory()->create();
        $scropableNotAllowed = ScopableMany::factory()->create();
        $user = User::factory()->hasAttached($scropableAllowed, [], 'scopable_manies')->create();
        $entityToInclude = EntityMany::factory()->hasAttached($scropableAllowed, [], 'scopable_manies')->create();
        $entityNotToInclude = EntityMany::factory()->hasAttached($scropableNotAllowed, [], 'scopable_manies')->create();
        
        $this->actingAs($user);

        $this->get(route('entity_manies.index'))
            ->assertSuccessful()
            ->assertJsonFragment($this->entryArray($entityToInclude))
            ->assertJsonMissing($this->entryArray($entityNotToInclude));
    }

    public function test_scopable_show_one_to_many(): void
    {
        $scropableAllowed = ScopableOne::factory()->create();
        $scropableNotAllowed = ScopableOne::factory()->create();
        $user = User::factory()->for($scropableAllowed, 'scopable_one')->create();
        $entityToInclude = EntityOne::factory()->for($scropableAllowed, 'scopable_one')->create();
        $entityNotToInclude = EntityOne::factory()->for($scropableNotAllowed, 'scopable_one')->create();
        
        $this->actingAs($user);

        $this->get(route('entity_ones.show', ['entity_one' => $entityToInclude->id]))
            ->assertSuccessful()
            ->assertJsonFragment($entityToInclude->toArray());
        $this->get(route('entity_ones.show', ['entity_one' => $entityNotToInclude->id]))
            ->assertForbidden();
    }

    public function test_scopable_show_many_to_many(): void
    {
        $scropableAllowed = ScopableMany::factory()->create();
        $scropableNotAllowed = ScopableMany::factory()->create();
        $user = User::factory()->hasAttached($scropableAllowed, [], 'scopable_manies')->create();
        $entityToInclude = EntityMany::factory()->hasAttached($scropableAllowed, [], 'scopable_manies')->create();
        $entityNotToInclude = EntityMany::factory()->hasAttached($scropableNotAllowed, [], 'scopable_manies')->create();
        
        $this->actingAs($user);

        $this->get(route('entity_manies.show', ['entity_many' => $entityToInclude->id]))
            ->assertSuccessful()
            ->assertJsonFragment($entityToInclude->toArray());
        $this->get(route('entity_manies.show', ['entity_many' => $entityNotToInclude->id]))
            ->assertForbidden();
    }

    public function test_scopable_store_one_to_many(): void
    {
        $scropableAllowed = ScopableOne::factory()->create();
        $scropableNotAllowed = ScopableOne::factory()->create();
        $user = User::factory()->for($scropableAllowed, 'scopable_one')->create();
        $entityToInclude = EntityOne::factory()->for($scropableAllowed, 'scopable_one')->make();
        $entityNotToInclude = EntityOne::factory()->for($scropableNotAllowed, 'scopable_one')->make();
        
        $this->actingAs($user);

        $this->post(route('entity_ones.store', $entityToInclude->toArray()))
            ->assertSuccessful()
            ->assertJsonFragment($entityToInclude->toArray());
        $this->assertDatabaseHas('entity_ones', $entityToInclude->toArray());
        $this->post(route('entity_ones.store', $entityNotToInclude->toArray()))
            ->assertForbidden();
        $this->assertDatabaseMissing('entity_ones', $entityNotToInclude->toArray());
    }

    public function test_scopable_store_many_to_many(): void
    {
        $scropableAllowed = ScopableMany::factory()->create();
        $scropableNotAllowed = ScopableMany::factory()->create();
        $user = User::factory()->hasAttached($scropableAllowed, [], 'scopable_manies')->create();
        $entityToInclude = EntityMany::factory()->make();
        $entityNotToInclude = EntityMany::factory()->make();
        
        $this->actingAs($user);

        $id = $this->post(route('entity_manies.store', array_merge($entityToInclude->toArray(), ['scopable_manies' => [$scropableAllowed->id]])))
            ->assertSuccessful()
            ->assertJsonFragment($entityToInclude->toArray())
            ->json()['id'];
        $this->assertDatabaseHas('entity_manies', $entityToInclude->toArray());
        $this->assertDatabaseHas('entity_many_scopable_many', [
            'scopable_many_id' => $scropableAllowed->id,
            'entity_many_id' => $id,
        ]);
        $this->post(route('entity_manies.store', $entityNotToInclude->toArray()))
            ->assertForbidden();
        $this->assertDatabaseMissing('entity_manies', $entityNotToInclude->toArray());
        $this->assertDatabaseMissing('entity_many_scopable_many', [
            'scopable_many_id' => $scropableNotAllowed->id,
        ]);
    }

    public function test_scopable_update_one_to_many(): void
    {
        $scropableAllowed = ScopableOne::factory()->create();
        $scropableNotAllowed = ScopableOne::factory()->create();
        $user = User::factory()->for($scropableAllowed, 'scopable_one')->create();
        $entityToInclude = EntityOne::factory()->for($scropableAllowed, 'scopable_one')->create();
        $entityNotToInclude = EntityOne::factory()->for($scropableNotAllowed, 'scopable_one')->create();
        
        $this->actingAs($user);

        $updatedToInclude = array_merge($this->entryArray($entityToInclude), ['name' => 'Test 1']);
        $this->put(route('entity_ones.update', array_merge($updatedToInclude, ['entity_one' => $entityToInclude->id])))
            ->assertSuccessful()
            ->assertJsonFragment($updatedToInclude);
        $this->assertDatabaseHas('entity_ones', $updatedToInclude);
        $updatedNotToInclude = array_merge($this->entryArray($entityNotToInclude), ['name' => 'Test 2']);
        $this->put(route('entity_ones.update', array_merge($updatedNotToInclude, ['entity_one' => $entityNotToInclude->id])))
            ->assertForbidden();
        $this->assertDatabaseMissing('entity_ones', $updatedNotToInclude);
    }

    public function test_scopable_update_many_to_many(): void
    {
        $scropableAllowed = ScopableMany::factory()->create();
        $scropableNotAllowed = ScopableMany::factory()->create();
        $user = User::factory()->hasAttached($scropableAllowed, [], 'scopable_manies')->create();
        $entityToInclude = EntityMany::factory()->hasAttached($scropableAllowed, [], 'scopable_manies')->create();
        $entityNotToInclude = EntityMany::factory()->hasAttached($scropableNotAllowed, [], 'scopable_manies')->create();
        
        $this->actingAs($user);

        $updatedToInclude = array_merge($this->entryArray($entityToInclude), ['name' => 'Test 1']);
        $this->put(route('entity_manies.update', array_merge($updatedToInclude, ['entity_many' => $entityToInclude->id])))
            ->assertSuccessful()
            ->assertJsonFragment($updatedToInclude);
        $this->assertDatabaseHas('entity_manies', $updatedToInclude);
        $updatedNotToInclude = array_merge($this->entryArray($entityNotToInclude), ['name' => 'Test 2']);
        $this->put(route('entity_manies.update', array_merge($updatedNotToInclude, ['entity_many' => $entityNotToInclude->id])))
            ->assertForbidden();
        $this->assertDatabaseMissing('entity_manies', $updatedNotToInclude);
    }

    public function test_scopable_update_many_to_many_relations(): void
    {
        $scropableInit = ScopableMany::factory()->create();
        $scropableAllowed = ScopableMany::factory()->create();
        $scropableNotAllowed = ScopableMany::factory()->create();
        $user = User::factory()->hasAttached([$scropableInit, $scropableAllowed], [], 'scopable_manies')->create();
        $entityToInclude = EntityMany::factory()->hasAttached($scropableInit, [], 'scopable_manies')->create();
        $entityNotToInclude = EntityMany::factory()->hasAttached($scropableInit, [], 'scopable_manies')->create();
        
        $this->actingAs($user);

        $this->put(route('entity_manies.update', array_merge($entityToInclude->toArray(), [
            'scopable_manies' => [$scropableAllowed->id],
            'entity_many' => $entityToInclude->id,
        ])))
            ->assertSuccessful()
            ->assertJsonFragment($entityToInclude->toArray());
        $this->assertDatabaseHas('entity_many_scopable_many', [
            'scopable_many_id' => $scropableAllowed->id,
            'entity_many_id' => $entityToInclude->id,
        ]);
        $this->put(route('entity_manies.update', array_merge($entityNotToInclude->toArray(), [
            'scopable_manies' => [$scropableNotAllowed->id],
            'entity_many' => $entityToInclude->id,
        ])))
            ->assertForbidden();
        $this->assertDatabaseMissing('entity_many_scopable_many', [
            'scopable_many_id' => $scropableNotAllowed->id,
            'entity_many_id' => $entityNotToInclude->id,
        ]);
        $this->assertDatabaseHas('entity_many_scopable_many', [
            'scopable_many_id' => $scropableInit->id,
            'entity_many_id' => $entityNotToInclude->id,
        ]);

    }

    public function test_scopable_delete_one_to_many(): void
    {
        $scropableAllowed = ScopableOne::factory()->create();
        $scropableNotAllowed = ScopableOne::factory()->create();
        $user = User::factory()->for($scropableAllowed, 'scopable_one')->create();
        $entityToInclude = EntityOne::factory()->for($scropableAllowed, 'scopable_one')->create();
        $entityNotToInclude = EntityOne::factory()->for($scropableNotAllowed, 'scopable_one')->create();
        
        $this->actingAs($user);

        $this->delete(route('entity_ones.destroy', array_merge($entityToInclude->toArray(), ['entity_one' => $entityToInclude->id])))
            ->assertSuccessful();
        $this->assertDatabaseMissing('entity_ones', $this->entryArray($entityToInclude));
        $this->delete(route('entity_ones.destroy', array_merge($entityNotToInclude->toArray(), ['entity_one' => $entityNotToInclude->id])))
            ->assertForbidden();
        $this->assertDatabaseHas('entity_ones', $this->entryArray($entityNotToInclude));
    }

    public function test_scopable_delete_many_to_many(): void
    {
        $scropableAllowed = ScopableMany::factory()->create();
        $scropableNotAllowed = ScopableMany::factory()->create();
        $user = User::factory()->hasAttached($scropableAllowed, [], 'scopable_manies')->create();
        $entityToInclude = EntityMany::factory()->hasAttached($scropableAllowed, [], 'scopable_manies')->create();
        $entityNotToInclude = EntityMany::factory()->hasAttached($scropableNotAllowed, [], 'scopable_manies')->create();
        
        $this->actingAs($user);

        $this->delete(route('entity_manies.destroy', array_merge($entityToInclude->toArray(), ['entity_many' => $entityToInclude->id])))
            ->assertSuccessful();
        $this->assertDatabaseMissing('entity_manies', $this->entryArray($entityToInclude));
        $this->assertDatabaseMissing('entity_many_scopable_many', [
            'scopable_many_id' => $scropableAllowed->id,
            'entity_many_id' => $entityToInclude->id,
        ]);
        $this->delete(route('entity_manies.destroy', array_merge($entityNotToInclude->toArray(), ['entity_many' => $entityNotToInclude->id])))
            ->assertForbidden();
        $this->assertDatabaseHas('entity_manies', $this->entryArray($entityNotToInclude));
        $this->assertDatabaseHas('entity_many_scopable_many', [
            'scopable_many_id' => $scropableNotAllowed->id,
            'entity_many_id' => $entityNotToInclude->id,
        ]);
    }

    public function test_scopable_delete_many_one_to_many(): void
    {
        $scropableAllowed = ScopableOne::factory()->create();
        $scropableNotAllowed = ScopableOne::factory()->create();
        $user = User::factory()->for($scropableAllowed, 'scopable_one')->create();
        $entityToInclude = EntityOne::factory()->for($scropableAllowed, 'scopable_one')->create();
        $entityToInclude2 = EntityOne::factory()->for($scropableAllowed, 'scopable_one')->create();
        $entityToInclude3 = EntityOne::factory()->for($scropableAllowed, 'scopable_one')->create();
        $entityNotToInclude = EntityOne::factory()->for($scropableNotAllowed, 'scopable_one')->create();
        
        $this->actingAs($user);

        $this->delete(route('entity_ones.destroy_many', array_merge($entityToInclude->toArray(), ['ids' => [
            $entityToInclude->id, $entityToInclude2->id,
        ]])))
            ->assertSuccessful();
        $this->assertDatabaseMissing('entity_ones', $this->entryArray($entityToInclude));
        $this->assertDatabaseMissing('entity_ones', $this->entryArray($entityToInclude2));
        $this->delete(route('entity_ones.destroy_many', array_merge($entityNotToInclude->toArray(), ['ids' => [
            $entityToInclude3->id, $entityNotToInclude->id,
        ]])))
            ->assertForbidden();
        $this->assertDatabaseHas('entity_ones', $this->entryArray($entityToInclude3));
        $this->assertDatabaseHas('entity_ones', $this->entryArray($entityNotToInclude));
    }

    public function test_scopable_delete_many_many_to_many(): void
    {
        $scropableAllowed = ScopableMany::factory()->create();
        $scropableNotAllowed = ScopableMany::factory()->create();
        $user = User::factory()->hasAttached($scropableAllowed, [], 'scopable_manies')->create();
        $entityToInclude = EntityMany::factory()->hasAttached($scropableAllowed, [], 'scopable_manies')->create();
        $entityToInclude2 = EntityMany::factory()->hasAttached($scropableAllowed, [], 'scopable_manies')->create();
        $entityToInclude3 = EntityMany::factory()->hasAttached($scropableAllowed, [], 'scopable_manies')->create();
        $entityNotToInclude = EntityMany::factory()->hasAttached($scropableNotAllowed, [], 'scopable_manies')->create();
        
        $this->actingAs($user);

        $this->delete(route('entity_manies.destroy_many', array_merge($entityToInclude->toArray(), ['ids' => [
            $entityToInclude->id, $entityToInclude2->id, 
        ]])))
            ->assertSuccessful();
        $this->assertDatabaseMissing('entity_manies', $this->entryArray($entityToInclude));
        $this->assertDatabaseMissing('entity_manies', $this->entryArray($entityToInclude2));
        $this->assertDatabaseMissing('entity_many_scopable_many', [
            'scopable_many_id' => $scropableAllowed->id,
            'entity_many_id' => $entityToInclude->id,
        ]);
        $this->assertDatabaseMissing('entity_many_scopable_many', [
            'scopable_many_id' => $scropableAllowed->id,
            'entity_many_id' => $entityToInclude2->id,
        ]);
        $this->delete(route('entity_manies.destroy_many', array_merge($entityNotToInclude->toArray(), [ 'ids' => [
            $entityToInclude3->id, $entityNotToInclude->id, 
        ]])))
            ->assertForbidden();
        $this->assertDatabaseHas('entity_manies', $this->entryArray($entityToInclude3));
        $this->assertDatabaseHas('entity_manies', $this->entryArray($entityNotToInclude));
        $this->assertDatabaseHas('entity_many_scopable_many', [
            'scopable_many_id' => $scropableAllowed->id,
            'entity_many_id' => $entityToInclude3->id,
        ]);
        $this->assertDatabaseHas('entity_many_scopable_many', [
            'scopable_many_id' => $scropableNotAllowed->id,
            'entity_many_id' => $entityNotToInclude->id,
        ]);
    }
}
