<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Review extends Model
{
    use BumpsContentCache, HasFactory, HasTranslations;

    protected $guarded = [];

    public array $translatable = ['name', 'city', 'title', 'body'];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'reviewed_on' => 'date',
            'is_featured' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }

    /** Visible on the public site. */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    public function scopeNewestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('reviewed_on')->orderByDesc('rating')->orderBy('sort_order');
    }

    public function scopeHighestRated(Builder $query): Builder
    {
        return $query->orderByDesc('rating')->orderByDesc('reviewed_on')->orderBy('sort_order');
    }

    /**
     * Five-star stories from different destinations, in curated order.
     * Used by the home page and Why Us.
     */
    public static function stories(int $limit = 3): \Illuminate\Support\Collection
    {
        return static::approved()->with('destination')->where('rating', 5)->orderBy('sort_order')->get()
            ->unique('destination_id')->take($limit)->values();
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }
}
