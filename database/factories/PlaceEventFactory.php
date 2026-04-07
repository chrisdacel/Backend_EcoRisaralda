<?php

namespace Database\Factories;

use App\Models\PlaceEvent;
use App\Models\TuristicPlace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlaceEvent>
 */
class PlaceEventFactory extends Factory
{
    protected $model = PlaceEvent::class;

    public function definition(): array
    {
        return [
            'place_id' => TuristicPlace::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'starts_at' => now()->addDays(fake()->numberBetween(1, 30)),
            'ends_at' => now()->addDays(fake()->numberBetween(31, 60)),
            'approval_status' => 'approved',
        ];
    }

    /**
     * Indicate that the event is pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'approval_status' => 'pending',
        ]);
    }
}
