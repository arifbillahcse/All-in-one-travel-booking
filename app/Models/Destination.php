<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Destination extends Model
{
    use HasFactory, HasTranslations;

    protected $guarded = [];

    /** Columns stored as {"en": ..., "bn": ...}. */
    public array $translatable = [
        'name', 'region', 'tagline', 'summary', 'overview_title', 'duration', 'best_time', 'distance', 'style',
        'overview', 'highlights', 'attractions', 'itinerary', 'included', 'excluded',
        'seasons', 'transport', 'tips', 'faq', 'gallery',
    ];

    protected function casts(): array
    {
        return [
            'price_from' => 'integer',
            'related' => 'array',
            'is_published' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->orderBy('sort_order');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /** Destinations suggested at the bottom of this page, in the stored order. */
    public function relatedDestinations(): Collection
    {
        $slugs = $this->related ?? [];

        return static::published()->whereIn('slug', $slugs)->get()
            ->sortBy(fn (self $d) => array_search($d->slug, $slugs, true))
            ->values();
    }
}
