<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Package extends Model
{
    use BumpsContentCache, HasFactory, HasTranslations;

    protected $guarded = [];

    public array $translatable = [
        'name', 'description', 'best_for', 'badge', 'features', 'hotel', 'meals', 'transport', 'cancellation',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'has_guide' => 'boolean',
            'has_tickets' => 'boolean',
            'has_airport_transfer' => 'boolean',
            'has_trip_manager' => 'boolean',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
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
}
