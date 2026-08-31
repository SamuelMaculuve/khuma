<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompaniesFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'tax_number' => $this->faker->numerify('###########'),
            'address' => $this->faker->address(),
            'email' => $this->faker->companyEmail(),
        ];
    }
}