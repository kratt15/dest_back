<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Provider>
 */
class ProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $table->id();
        // $table->string('name_provider');
        // $table->string('name_resp');
        // $table->string('address_provider');
        // $table->string('phone_provider');
        // $table->string('email_provider')->nullable();
        // $table->timestamps();
        return [

            'name_provider' => $this->faker->company(),
            'name_resp' => $this->faker->name(),
            'address_provider' => $this->faker->address(),
            'phone_provider' => $this->faker->phoneNumber(),
            'email_provider' => $this->faker->unique()->safeEmail()



        ];
    }
}
