<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeds all public content. Safe to run repeatedly: every seeder updates by slug.
     */
    public function run(): void
    {
        $this->call([
            DestinationSeeder::class,
            PackageSeeder::class,
            ReviewSeeder::class,
            PostSeeder::class,
        ]);
    }
}
