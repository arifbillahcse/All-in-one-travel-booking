<?php

namespace App\Models\Concerns;

use App\Support\ContentCache;

/** Invalidates cached content whenever a record is saved or deleted. */
trait BumpsContentCache
{
    protected static function bootBumpsContentCache(): void
    {
        $bump = fn () => ContentCache::bump();

        static::saved($bump);
        static::deleted($bump);
    }
}
