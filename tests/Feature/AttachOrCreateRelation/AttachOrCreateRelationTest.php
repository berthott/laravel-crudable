<?php

namespace berthott\Crudable\Tests\Feature\AttachOrCreateRelation;

use Illuminate\Support\Facades\Route;

class AttachOrCreateRelationTest extends TestCase
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

    public function test_create_tags(): void
    {
        $tag = 'TestTag';
        $userToStore = User::factory()->make([
            'tags' => [$tag],
        ]);
        $id = $this->post(route('users.store'), $userToStore->toArray())
            ->assertStatus(201)
            ->assertJsonFragment(['name' => $tag])
            ->json()['id'];
        $this->assertDatabaseHas('users', [
            'id' => $id,
            'name' => $userToStore->name
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => $tag
        ]);
        $this->assertDatabaseHas('taggables', [
            'taggable_id' => $id
        ]);
    }

    public function test_create_tag(): void
    {
        $tag = 'TestTag';
        $userToStore = User::factory()->make([
            'tag' => $tag,
        ]);
        $id = $this->post(route('users.store'), $userToStore->toArray())
            ->assertStatus(201)
            ->assertJsonFragment(['name' => $tag])
            ->json()['id'];
        $this->assertDatabaseHas('users', [
            'id' => $id,
            'name' => $userToStore->name
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => $tag
        ]);
        $this->assertDatabaseHas('taggables', [
            'taggable_id' => $id
        ]);
    }

    public function test_update_tag(): void
    {
        $update = 'TestTag5000';
        $user = User::factory()->create();
        $tags = Tag::factory()->count(2)->create();
        $attributes = Attribute::factory()->create();
        $user->tags()->attach($tags);
        $user->attributes()->attach($attributes);
        $this->put(route('users.update', ['user' => $user->id]), [
            'tags' => [$tags[0]->name, $update]
        ])->assertStatus(200);
        $this->assertDatabaseCount('tags', 2);
        $this->assertDatabaseCount('attributes', 1);
        $this->assertDatabaseHas('tags', [
            'name' => $tags[0]->name,
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => $update,
        ]);
        $this->assertDatabaseMissing('tags', [
            'name' => $tags[1]->name,
        ]);
    }

    public function test_update_tag_with_multiple_morph_relations(): void
    {
        $update = 'TestTag5000';
        $user = User::factory()->create();
        $project = Project::factory()->create();
        $userTags = Tag::factory()->count(2)->create();
        $projectTags = Tag::factory()->count(2)->create();
        $user->tags()->attach($userTags);
        $project->tags()->attach($projectTags);
        $this->assertDatabaseCount('tags', 4);
        $this->put(route('users.update', ['user' => $user->id]), [
            'tags' => [$userTags[0]->name, $update]
        ])->assertStatus(200);
        $this->assertDatabaseCount('tags', 4);
        $this->assertDatabaseHas('tags', [
            'name' => $userTags[0]->name,
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => $update,
        ]);
        $this->assertDatabaseMissing('tags', [
            'name' => $userTags[1]->name,
        ]);
    }

    public function test_delete_tag(): void
    {
        $user = User::factory()->create();
        $tags = Tag::factory()->count(2)->create();
        $user->tags()->attach($tags);
        $this->put(route('users.update', ['user' => $user->id]), [
            'tags' => []
        ])->assertStatus(200);
        $this->assertDatabaseCount('tags', 0);
        $this->assertDatabaseMissing('tags', [
            'name' => $tags[0]->name,
        ]);
        $this->assertDatabaseMissing('tags', [
            'name' => $tags[1]->name,
        ]);
    }

    public function test_delete_user_and_related_tags(): void
    {
        $user = User::factory()->for(Method::factory())->create();
        $tags = Tag::factory()->count(2)->create();
        $user->tags()->attach($tags);
        $this->delete(route('users.destroy', ['user' => $user->id]))
            ->assertStatus(200);
        $this->assertDatabaseCount('methods', 0);
        $this->assertDatabaseCount('tags', 0);
        $this->assertDatabaseMissing('tags', [
            'name' => $tags[0]->name,
        ]);
        $this->assertDatabaseMissing('tags', [
            'name' => $tags[1]->name,
        ]);
    }

    public function test_create_method(): void
    {
        $method = 'TestMethod';
        $userToStore = User::factory()->make([
            'method' => $method,
        ]);
        $id = $this->post(route('users.store'), $userToStore->toArray())
            ->assertStatus(201)
            ->assertJsonFragment(['name' => $method])
            ->json()['id'];
        $this->assertDatabaseHas('users', [
            'id' => $id,
            'name' => $userToStore->name
        ]);
        $this->assertDatabaseHas('methods', [
            'name' => $method
        ]);
    }

    public function test_update_method(): void
    {
        $update = 'TestMethod5000';
        $user = User::factory()->for(Method::factory())->create();
        $this->assertDatabaseCount('methods', 1);
        $this->put(route('users.update', ['user' => $user->id]), [
            'method' => $update
        ])->assertStatus(200);
        $this->assertDatabaseCount('methods', 1);
        $this->assertDatabaseHas('methods', [
            'name' => $update,
        ]);
    }

    public function test_delete_method(): void
    {
        $user = User::factory()->for(Method::factory())->create();
        $this->put(route('users.update', ['user' => $user->id]), [
            'method' => null
        ])->assertStatus(200);
        $this->assertDatabaseCount('methods', 0);
    }
}
