<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Caches data derived from the content tables. Every key carries a version number that
 * changes whenever content is saved or deleted (see BumpsContentCache), so admin edits
 * show up immediately without clearing the whole cache.
 */
final class ContentCache
{
    private const VERSION_KEY = 'travelorio.content.version';

    public static function version(): string
    {
        return (string) Cache::rememberForever(self::VERSION_KEY, fn () => (string) microtime(true));
    }

    public static function bump(): void
    {
        Cache::forever(self::VERSION_KEY, (string) microtime(true));
    }

    /** @template T @param \Closure(): T $callback @return T */
    public static function remember(string $key, int $seconds, \Closure $callback): mixed
    {
        return Cache::remember('travelorio.'.self::version().'.'.app()->getLocale().'.'.$key, $seconds, $callback);
    }
}
