<?php

namespace berthott\Crudable\Tests\Feature\Scopeable;

use Illuminate\Database\Eloquent\Factories\Factory;

class EntityManyFactory extends Factory
{
    protected $model = EntityMany::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
