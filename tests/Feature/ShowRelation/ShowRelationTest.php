<?php

namespace berthott\Crudable\Tests\Feature\ShowRelation;

use Illuminate\Support\Facades\Route;

class ShowRelationTest extends TestCase
{
    public function test_show_relations_succuss(): void
    {
        $tags = Tag::factory()->count(2)->create();
        $this->assertModelExists($tags[0]);
        $user = User::factory()->create();
        $user->tags()->attach($tags);
        $this->assertModelExists($user);
        $this->assertDatabaseHas('tag_user', [
            'tag_id' => $tags[0]->id,
            'user_id' => $user->id,
        ]);
        $this->get(route('users.show', ['user' => $user->id]))
        ->assertStatus(200)
        ->assertJsonCount(2, 'tags');
    }

    public function test_show_relations_fail(): void
    {
        $tags = Tag::factory()->count(2)->create();
        $this->assertModelExists($tags[0]);
        $role = Role::factory()->create();
        $role->tags()->attach($tags);
        $this->assertModelExists($role);
        $this->assertDatabaseHas('role_tag', [
            'tag_id' => $tags[0]->id,
            'role_id' => $role->id,
        ]);
        $this->get(route('roles.show', ['role' => $role->id]))
        ->assertStatus(200)
        ->assertJsonMissing(['tags']);
    }

    public function test_show_relation_deleted(): void
    {
        $tags = Tag::factory()->count(2)->create();
        $this->assertModelExists($tags[0]);
        $user = User::factory()->create();
        $user->tags()->attach($tags);
        $this->assertModelExists($user);
        $this->assertDatabaseHas('tag_user', [
            'tag_id' => $tags[0]->id,
            'user_id' => $user->id
        ]);
        $this->delete(route('tags.destroy', ['tag' => $tags[0]->id]))
        ->assertStatus(200);
        $this->assertDatabaseMissing('tag_user', [
            'tag_id' => $tags[0]->id,
            'user_id' => $user->id,
        ]);
        $this->get(route('users.show', ['user' => $user->id]))
         ->assertStatus(200)
        ->assertJsonCount(1, 'tags');
    }
}
