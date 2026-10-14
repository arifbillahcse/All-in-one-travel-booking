<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Review> */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'name' => ['en' => $name, 'bn' => $name],
            'city' => ['en' => 'Dhaka', 'bn' => 'ঢাকা'],
            'title' => ['en' => fake()->sentence(4), 'bn' => 'চমৎকার ভ্রমণ'],
            'body' => ['en' => fake()->paragraph(), 'bn' => 'খুব ভালো অভিজ্ঞতা ছিল।'],
            'rating' => 5,
            'traveler_type' => 'Family',
            'reviewed_on' => fake()->dateTimeBetween('-1 year', 'now'),
            'is_approved' => true,
        ];
    }
}
