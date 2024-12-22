<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'issuer_name' => $this->faker->company(),
            'issuer_document_type' => $this->faker->randomElement([1, 4, 6, 7]),
            'issuer_document_number' => $this->faker->randomNumber(8),
            'receiver_name' => $this->faker->name(),
            'receiver_document_type' => $this->faker->randomElement([1, 4, 6, 7]),
            'receiver_document_number' => $this->faker->randomNumber(8),
            'total_amount' => $this->faker->randomFloat(2, 10, 1000),
            'xml_content' => $this->faker->text(),
        ];
    }
}
