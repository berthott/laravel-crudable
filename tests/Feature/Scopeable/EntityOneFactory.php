<?php

namespace berthott\Crudable\Tests\Feature\Scopeable;

use Illuminate\Database\Eloquent\Factories\Factory;

class EntityOneFactory extends Factory
{
    protected $model = EntityOne::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
