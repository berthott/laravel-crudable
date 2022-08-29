<?php

namespace berthott\Crudable\Tests\Feature\Scopable;

use Illuminate\Database\Eloquent\Factories\Factory;

class ScopableOneFactory extends Factory
{
    protected $model = ScopableOne::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
