<?php

namespace berthott\Crudable\Tests\Feature\AttachRelation;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
