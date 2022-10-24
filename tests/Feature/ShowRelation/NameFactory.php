<?php

namespace berthott\Crudable\Tests\Feature\ShowRelation;

use Illuminate\Database\Eloquent\Factories\Factory;

class NameFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
