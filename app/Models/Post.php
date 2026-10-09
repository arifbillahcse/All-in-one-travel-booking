<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentCache;
use App\Models\Concerns\HasImages;
use App\Support\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Translatable\HasTranslations;

class Post extends Model implements HasMedia
{
    use BumpsContentCache, HasFactory, HasImages, HasTranslations;

    protected $guarded = [];

    public array $translatable = ['title', 'excerpt', 'body'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'read_minutes' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    protected function imageWidths(): array
    {
        return ['cover' => [600, 1000, 1200]];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile()->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function coverImage(): Image
    {
        return $this->image('cover', $this->cover_image ?: placeholder_image('blog-'.$this->slug, 1200, 700));
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Published and not scheduled for the future, newest first. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()))
            ->orderByDesc('published_at');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }

    public function destination(): BelongsTo
    {
        return $this->belongsTo(Destination::class);
    }
}
