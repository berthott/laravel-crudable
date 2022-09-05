<?php

namespace berthott\Crudable\Tests\Feature\Scopeable;

use Illuminate\Database\Eloquent\Factories\Factory;

class ScopeableManyFactory extends Factory
{
    protected $model = ScopeableMany::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
