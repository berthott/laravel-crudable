<?php

namespace berthott\Crudable\Tests\Feature\AttachOrCreateRelation;

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
