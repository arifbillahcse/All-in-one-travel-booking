<?php

namespace Database\Factories;

use App\Models\Inquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Inquiry> */
class InquiryFactory extends Factory
{
    protected $model = Inquiry::class;

    public function definition(): array
    {
        return [
            'type' => Inquiry::TYPE_BOOKING,
            'status' => 'new',
            'name' => fake()->name(),
            'phone' => '+88017'.fake()->numerify('########'),
            'email' => fake()->safeEmail(),
            'travel_date' => fake()->dateTimeBetween('+1 month', '+6 months'),
            'guests' => fake()->numberBetween(1, 8),
            'locale' => fake()->randomElement(['en', 'bn']),
        ];
    }

    public function contact(): static
    {
        return $this->state(fn () => ['type' => Inquiry::TYPE_CONTACT, 'topic' => 'Planning a trip', 'message' => fake()->sentence(12)]);
    }
}
