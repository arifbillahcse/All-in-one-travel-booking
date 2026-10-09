<?php

namespace App\Models;

use App\Models\Concerns\BumpsContentCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Key/value site settings editable from the admin panel (Phase 7).
 * Values override the defaults in config/travelorio.php through the site() helper.
 */
class Setting extends Model
{
    use BumpsContentCache;

    private const CACHE_KEY = 'travelorio.settings';

    protected $guarded = [];

    protected function casts(): array
    {
        return ['value' => 'json'];
    }

    protected static function booted(): void
    {
        $flush = fn () => Cache::forget(self::CACHE_KEY);
        static::saved($flush);
        static::deleted($flush);
    }

    /** @return array<string, mixed> every stored setting, cached. */
    public static function overrides(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->pluck('value', 'key')->all());
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::overrides()[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
