<?php

namespace berthott\Crudable\Tests\Feature\Scopable;

use Illuminate\Database\Eloquent\Factories\Factory;

class ScopableManyFactory extends Factory
{
    protected $model = ScopableMany::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
