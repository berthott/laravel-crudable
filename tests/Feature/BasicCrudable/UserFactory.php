<?php

namespace berthott\Crudable\Tests\Feature\BasicCrudable;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'firstname' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'hours' => $this->faker->randomNumber(3),
        ];
    }
}
