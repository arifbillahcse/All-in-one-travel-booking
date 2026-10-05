<?php

namespace Database\Seeders;

use App\Models\Destination;
use Database\Seeders\Concerns\LoadsLegacyContent;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    use LoadsLegacyContent;

    public function run(): void
    {
        foreach ($this->legacy('destinations') as $row) {
            ['en' => $en, 'bn' => $bn] = $row;

            // Fields the Bangla export leaves out because they never change per language.
            foreach ($bn['attractions'] as $i => &$attraction) {
                $attraction['img'] = $en['attractions'][$i]['img'] ?? null;
            }
            foreach ($bn['seasons'] as $i => &$season) {
                $season['tone'] = $en['seasons'][$i]['tone'];
            }
            unset($attraction, $season);

            $destination = Destination::firstOrNew(['slug' => $en['slug']]);
            $destination->fill([
                'price_from' => $en['price'],
                'related' => $en['related'],
                'hero_image' => $en['heroImage'] ?? null,
                'card_image' => $en['cardImage'] ?? null,
                'sort_order' => $row['sort'],
                'is_published' => true,
            ]);

            $columns = [
                'name' => 'name', 'region' => 'region', 'tagline' => 'tagline',
                'overview_title' => 'overviewTitle', 'duration' => 'duration', 'best_time' => 'bestTime',
                'distance' => 'distance', 'style' => 'style', 'overview' => 'overview',
                'highlights' => 'highlights', 'attractions' => 'attractions', 'itinerary' => 'itinerary',
                'included' => 'included', 'excluded' => 'excluded', 'seasons' => 'seasons',
                'transport' => 'transport', 'tips' => 'tips', 'faq' => 'faq', 'gallery' => 'gallery',
            ];
            foreach ($columns as $column => $key) {
                $destination->setTranslations($column, ['en' => $en[$key], 'bn' => $bn[$key]]);
            }

            $destination->save();
        }
    }
}
