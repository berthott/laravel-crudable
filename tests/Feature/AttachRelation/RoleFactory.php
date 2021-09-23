<?php

namespace berthott\Crudable\Tests\Feature\AttachRelation;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
