<?php

namespace Database\Factories;

use App\Models\TuristicPlace;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TuristicPlace>
 */
class TuristicPlaceFactory extends Factory
{
    protected $model = TuristicPlace::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slogan' => fake()->catchPhrase(),
            'cover' => 'covers/test.webp',
            'description' => fake()->paragraph(3),
            'localization' => 'Risaralda, Colombia',
            'lat' => fake()->latitude(4.7, 5.1),
            'lng' => fake()->longitude(-76.1, -75.5),
            'Weather' => fake()->sentence(),
            'Weather_img' => 'weather/test.webp',
            'features' => fake()->sentence(),
            'features_img' => 'features/test.webp',
            'flora' => fake()->sentence(),
            'flora_img' => 'flora/test.webp',
            'estructure' => fake()->sentence(),
            'estructure_img' => 'structure/test.webp',
            'tips' => fake()->sentence(),
            'contact_info' => fake()->phoneNumber(),
            'user_id' => User::factory(),
            'approval_status' => 'approved',
        ];
    }

    /**
     * Indicate that the place is pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'approval_status' => 'pending',
        ]);
    }
}
