<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Review;
use Carbon\Carbon;
use Database\Seeders\Concerns\LoadsLegacyContent;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    use LoadsLegacyContent;

    public function run(): void
    {
        $destinations = Destination::pluck('id', 'slug');

        foreach ($this->legacy('reviews') as $row) {
            ['en' => $en, 'bn' => $bn] = $row;

            // Idempotent: the legacy data has no id, so name + date identify a review.
            $review = Review::query()
                ->where('name->en', $en['name'])
                ->whereDate('reviewed_on', Carbon::createFromFormat('Y-m', $en['date'])->startOfMonth())
                ->firstOrNew();

            $review->fill([
                'destination_id' => $destinations[$en['slug']] ?? null,
                'rating' => $en['rating'],
                'traveler_type' => $en['type'] ?? null,
                'reviewed_on' => Carbon::createFromFormat('Y-m', $en['date'])->startOfMonth(),
                'is_featured' => $en['featured'] ?? false,
                'is_approved' => true,
                'sort_order' => $row['sort'],
            ]);

            foreach (['name' => 'name', 'city' => 'city', 'title' => 'title', 'body' => 'text'] as $column => $key) {
                $review->setTranslations($column, ['en' => $en[$key], 'bn' => $bn[$key]]);
            }

            $review->save();
        }
    }
}
