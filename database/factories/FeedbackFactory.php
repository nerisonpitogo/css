<?php

namespace Database\Factories;

use App\Models\LibRegion\LibRegion;
use App\Models\OfficeService\OfficeService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feedback>
 */
class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'client_type' => $this->faker->randomElement(['internal', 'external']),
            // 'is_external' => $this->faker->boolean,
            // 'sex' => $this->faker->randomElement(['male', 'female']),
            // 'age' => $this->faker->numberBetween(18, 99),
            // 'region_id' => LibRegion::get()->random()->id,
            // 'office_service_id' => OfficeService::get()->random()->id,
            // 'cc1' => $this->faker->numberBetween(1, 6),
            // 'cc2' => $this->faker->optional()->numberBetween(1, 6),
            // 'cc3' => $this->faker->optional()->numberBetween(1, 6),
            // 'sqd0' => $this->faker->numberBetween(1, 6),
            // 'sqd1' => $this->faker->numberBetween(1, 6),
            // 'sqd2' => $this->faker->numberBetween(1, 6),
            // 'sqd3' => $this->faker->numberBetween(1, 6),
            // 'sqd4' => $this->faker->numberBetween(1, 6),
            // 'sqd5' => $this->faker->numberBetween(1, 6),
            // 'sqd6' => $this->faker->numberBetween(1, 6),
            // 'sqd7' => $this->faker->numberBetween(1, 6),
            // 'sqd8' => $this->faker->numberBetween(1, 6),
            // 'suggestions' => $this->faker->optional()->sentence,
            // 'email' => $this->faker->optional()->safeEmail,
            // 'created_at' => now(),
            // 'updated_at' => now(),


            'client_type' => $this->faker->randomElement(['internal', 'external']),
            'is_external' => $this->faker->boolean,
            'sex' => $this->faker->randomElement(['male', 'female']),
            'age' => $this->faker->numberBetween(18, 99),
            'region_id' => LibRegion::get()->random()->id,
            'office_service_id' => OfficeService::get()->random()->id,
            'cc1' => $this->faker->numberBetween(1, 6),
            'cc2' => $this->faker->optional()->numberBetween(1, 6),
            'cc3' => $this->faker->optional()->numberBetween(1, 6),
            'sqd0' => $this->faker->numberBetween(1, 6),
            'sqd1' => $this->faker->numberBetween(1, 6),
            'sqd2' => $this->faker->numberBetween(1, 6),
            'sqd3' => $this->faker->numberBetween(1, 6),
            'sqd4' => $this->faker->numberBetween(1, 6),
            'sqd5' => $this->faker->numberBetween(1, 6),
            'sqd6' => $this->faker->numberBetween(1, 6),
            'sqd7' => $this->faker->numberBetween(1, 6),
            'sqd8' => $this->faker->numberBetween(1, 6),
            'suggestions' => $this->faker->optional()->sentence,
            'email' => $this->faker->optional()->safeEmail,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
