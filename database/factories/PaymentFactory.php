<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
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
            'ref_payment'=> $this->faker->unique()->regexify('[A-Za-z0-9]{10}'),
            'amount'=> $this->faker->numberbetween(20000, 1000000),
            'payment_date_time'=> $this->faker->dateTime(),
        ];
    }
}
