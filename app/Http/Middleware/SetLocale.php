<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * The language is decided by the URL: /bn/... is Bangla, everything else English.
 * Registered globally so error pages for unknown URLs are translated too.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->segment(1) === 'bn' ? 'bn' : 'en';

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
