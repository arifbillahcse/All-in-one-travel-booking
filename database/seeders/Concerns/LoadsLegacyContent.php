<?php

namespace Database\Seeders\Concerns;

use RuntimeException;

/**
 * Helpers for seeding content exported from the static site
 * (see tools/export-legacy-content.cjs).
 */
trait LoadsLegacyContent
{
    private static ?array $bnDictionary = null;

    /** Decode one of the JSON files in database/seeders/data. */
    protected function legacy(string $file): array
    {
        $path = database_path("seeders/data/{$file}.json");

        return json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * Build an {"en": ..., "bn": ...} pair. Bangla comes from the legacy UI dictionary
     * (English text is the key), and a missing translation fails the seed loudly
     * instead of silently shipping English on the Bangla site.
     */
    protected function pair(string $en): array
    {
        self::$bnDictionary ??= $this->legacy('bn-dictionary');

        if (! isset(self::$bnDictionary[$en])) {
            throw new RuntimeException("No Bangla translation for: {$en}");
        }

        return ['en' => $en, 'bn' => self::$bnDictionary[$en]];
    }

    /** Same as pair() for a list of strings. */
    protected function pairs(array $items): array
    {
        return [
            'en' => $items,
            'bn' => array_map(fn (string $s) => $this->pair($s)['bn'], $items),
        ];
    }
}
