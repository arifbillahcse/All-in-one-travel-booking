<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Package;
use Database\Seeders\Concerns\LoadsLegacyContent;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    use LoadsLegacyContent;

    public function run(): void
    {
        $plans = [
            [
                'slug' => 'weekend-escape', 'name' => 'Weekend Escape', 'description' => 'A quick, restful getaway.',
                'best_for' => 'Best for couples and short breaks', 'badge' => null,
                'price' => 5500, 'days' => 2, 'nights' => 1, 'destinations_count' => 1,
                'features' => ['2 days, 1 night', 'Standard hotel stay', 'Breakfast included', 'Local transport', '1 destination'],
                'hotel' => 'Standard', 'meals' => 'Breakfast', 'transport' => 'Local',
                'has_guide' => false, 'has_tickets' => false, 'has_airport_transfer' => false, 'has_trip_manager' => false,
                'is_featured' => false,
            ],
            [
                'slug' => 'explorer', 'name' => 'Explorer', 'description' => 'Our complete experience.',
                'best_for' => 'Best for families and friends', 'badge' => 'Most Popular',
                'price' => 12500, 'days' => 4, 'nights' => 3, 'destinations_count' => 1,
                'features' => ['4 days, 3 nights', 'Premium hotel or resort', 'All meals included', 'Private transport and guide', 'Entry tickets and activities'],
                'hotel' => 'Premium', 'meals' => 'All meals', 'transport' => 'Private AC vehicle',
                'has_guide' => true, 'has_tickets' => true, 'has_airport_transfer' => false, 'has_trip_manager' => false,
                'is_featured' => true,
            ],
            [
                'slug' => 'grand-bangladesh', 'name' => 'Grand Bangladesh', 'description' => 'Multiple destinations in one trip.',
                'best_for' => 'Best for first-time visitors', 'badge' => null,
                'price' => 28000, 'days' => 8, 'nights' => 7, 'destinations_count' => 3,
                'features' => ['8 days, 7 nights', '3 destinations of your choice', 'Luxury stays', 'Dedicated trip manager', 'Airport pickup and drop'],
                'hotel' => 'Luxury', 'meals' => 'All meals', 'transport' => 'Private AC vehicle',
                'has_guide' => true, 'has_tickets' => true, 'has_airport_transfer' => true, 'has_trip_manager' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($plans as $i => $plan) {
            $package = Package::firstOrNew(['slug' => $plan['slug']]);
            $package->fill([
                'price' => $plan['price'], 'days' => $plan['days'], 'nights' => $plan['nights'],
                'destinations_count' => $plan['destinations_count'],
                'has_guide' => $plan['has_guide'], 'has_tickets' => $plan['has_tickets'],
                'has_airport_transfer' => $plan['has_airport_transfer'], 'has_trip_manager' => $plan['has_trip_manager'],
                'is_featured' => $plan['is_featured'], 'sort_order' => $i + 1, 'is_published' => true,
            ]);

            foreach (['name', 'description', 'best_for', 'hotel', 'meals', 'transport'] as $field) {
                $package->setTranslations($field, $this->pair($plan[$field]));
            }
            $package->setTranslations('badge', $plan['badge'] ? $this->pair($plan['badge']) : ['en' => null, 'bn' => null]);
            $package->setTranslations('features', $this->pairs($plan['features']));
            $package->setTranslations('cancellation', $this->pair('7 days before'));
            $package->save();
        }

        $addons = [
            ['slug' => 'airport-transfer', 'icon' => 'plane', 'title' => 'Airport transfer', 'description' => 'Private pickup and drop in an AC car.', 'price' => 1500, 'unit' => null],
            ['slug' => 'trip-photographer', 'icon' => 'camera', 'title' => 'Trip photographer', 'description' => 'A professional to capture your best moments.', 'price' => 4000, 'unit' => ['en' => '/ day', 'bn' => '/ দিন']],
            ['slug' => 'private-boat', 'icon' => 'boat', 'title' => 'Private boat', 'description' => 'Skip the crowds with your own boat and crew.', 'price' => 3500, 'unit' => null],
            ['slug' => 'extra-night', 'icon' => 'bed', 'title' => 'Extra night', 'description' => 'Stay longer at the same hotel and rate.', 'price' => 2800, 'unit' => ['en' => '/ room', 'bn' => '/ রুম']],
        ];

        foreach ($addons as $i => $row) {
            $addon = Addon::firstOrNew(['slug' => $row['slug']]);
            $addon->fill(['icon' => $row['icon'], 'price_from' => $row['price'], 'sort_order' => $i + 1, 'is_published' => true]);
            $addon->setTranslations('title', $this->pair($row['title']));
            $addon->setTranslations('description', $this->pair($row['description']));
            $addon->setTranslations('price_unit', $row['unit'] ?? ['en' => null, 'bn' => null]);
            $addon->save();
        }
    }
}
