<?php

namespace berthott\Crudable\Tests\Feature\Scopeable;

use Illuminate\Database\Eloquent\Factories\Factory;

class ScopeableOneFactory extends Factory
{
    protected $model = ScopeableOne::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
