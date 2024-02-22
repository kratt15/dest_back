<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            //
            'name'=>$this->faker->word(),
            'reference'=>Str::random(10),
            'expiration_date'=>$this->faker->date(),
            'cost'=>$this->faker->numberbetween(20000, 50000),
            'price'=>$this->faker->numberbetween(20000, 50000),
            'description'=>$this->faker->sentence(10),
        ];
    }
}
