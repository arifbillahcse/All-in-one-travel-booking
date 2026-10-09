<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class PostCategory extends Model
{
    use BumpsContentCache, HasFactory, HasTranslations;

    protected $guarded = [];

    public array $translatable = ['name'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
