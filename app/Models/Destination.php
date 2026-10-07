<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentCache;
use App\Models\Concerns\HasImages;
use App\Support\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

class Destination extends Model implements HasMedia
{
    use BumpsContentCache, HasFactory, HasImages, HasTranslations;

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

    protected function imageWidths(): array
    {
        return ['hero' => [640, 1280, 1920], 'card' => [400, 800], 'gallery' => [520, 1040, 1200, 1600]];
    }

    public function registerMediaCollections(): void
    {
        foreach (['hero', 'card'] as $single) {
            $this->addMediaCollection($single)->singleFile()->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
        }
        $this->addMediaCollection('gallery')->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function heroImage(): ?Image
    {
        return $this->image('hero', $this->hero_image);
    }

    public function cardImage(): ?Image
    {
        return $this->image('card', $this->card_image);
    }

    /** Gallery photo number $position (0-based); falls back to the placeholder photo. */
    public function galleryImage(int $position, bool $wide = false): Image
    {
        $fallback = placeholder_image($this->slug.'-g'.($position + 1), $wide ? 800 : 520, 520);

        return $this->image('gallery', $fallback, $position);
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

        return static::published()->with('media')->whereIn('slug', $slugs)->get()
            ->sortBy(fn (self $d) => array_search($d->slug, $slugs, true))
            ->values();
    }
}
