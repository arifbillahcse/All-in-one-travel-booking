<?php

if (! function_exists('site')) {
    /**
     * Read a TravelOrio site setting, e.g. site('whatsapp') or site('social.facebook').
     */
    function site(string $key, mixed $default = null): mixed
    {
        // Admin-edited values (settings table) win over the config defaults.
        try {
            $overrides = \App\Models\Setting::overrides();
        } catch (\Throwable) {
            $overrides = []; // table not migrated yet (fresh install, CI)
        }

        return $overrides[$key] ?? config('travelorio.'.$key, $default);
    }
}

if (! function_exists('whatsapp_url')) {
    /**
     * Build a wa.me link for the configured number, optionally with a prefilled message.
     */
    function whatsapp_url(?string $message = null): string
    {
        $url = 'https://wa.me/'.preg_replace('/\D+/', '', (string) site('whatsapp'));

        return $message ? $url.'?text='.rawurlencode($message) : $url;
    }
}

if (! function_exists('asset_js')) {
    /**
     * URL of a legacy script copied from the static site (public/assets/js).
     */
    function asset_js(string $path): string
    {
        return asset('assets/js/'.ltrim($path, '/'));
    }
}

if (! function_exists('is_bn')) {
    function is_bn(): bool
    {
        return app()->getLocale() === 'bn';
    }
}

if (! function_exists('t')) {
    /**
     * Translate a UI string and fill {placeholders}:  t('{name} Tour Packages', ['name' => $n]).
     * The English text is the key (lang/bn.json); a missing translation falls back to English.
     */
    function t(string $key, array $vars = []): string
    {
        $text = __($key);

        foreach ($vars as $name => $value) {
            $text = str_replace('{'.$name.'}', (string) $value, $text);
        }

        return $text;
    }
}

if (! function_exists('lroute')) {
    /**
     * Locale-aware route(): English routes are "home", Bangla ones "bn.home".
     */
    function lroute(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        return route((is_bn() ? 'bn.' : '').$name, $parameters, $absolute);
    }
}

if (! function_exists('lurl')) {
    /** url() for a path inside the current language, e.g. lurl('destinations') -> /bn/destinations. */
    function lurl(string $path = ''): string
    {
        return url((is_bn() ? 'bn/' : '').ltrim($path, '/'));
    }
}

if (! function_exists('alternate_url')) {
    /**
     * The current page in another language (same route, parameters and query string).
     * Falls back to that language's home page on routes that have no counterpart (e.g. 404).
     */
    function alternate_url(string $locale): string
    {
        $prefix = $locale === 'bn' ? 'bn.' : '';
        $route = request()->route();
        $name = $route?->getName();

        if (! $name) {
            return route($prefix.'home');
        }

        $base = preg_replace('/^bn\./', '', $name);
        $query = request()->getQueryString();

        // Routes outside the public site (admin panel, health check) have no Bangla twin.
        if (! \Illuminate\Support\Facades\Route::has($prefix.$base)) {
            return route($prefix.'home');
        }

        return route($prefix.$base, $route->parameters()).($query ? '?'.$query : '');
    }
}

if (! function_exists('to_locale_digits')) {
    /** Convert ASCII digits to Bengali digits when the page is in Bangla. */
    function to_locale_digits(int|float|string $value): string
    {
        $value = (string) $value;

        return is_bn()
            ? strtr($value, ['0' => '০', '1' => '১', '2' => '২', '3' => '৩', '4' => '৪', '5' => '৫', '6' => '৬', '7' => '৭', '8' => '৮', '9' => '৯'])
            : $value;
    }
}

if (! function_exists('format_number')) {
    function format_number(int|float $value, int $decimals = 0): string
    {
        return to_locale_digits(number_format($value, $decimals));
    }
}

if (! function_exists('format_money')) {
    /** Taka amount: ৳12,500 in English, ৳১২,৫০০ in Bangla. */
    function format_money(int|float $amount): string
    {
        return '৳'.format_number($amount);
    }
}

if (! function_exists('format_date')) {
    /** Localised date, e.g. "March 12, 2026" / "১২ মার্চ, ২০২৬" ($format uses Carbon translatedFormat). */
    function format_date(\DateTimeInterface|string $date, ?string $format = null): string
    {
        $date = \Illuminate\Support\Carbon::parse($date)->locale(app()->getLocale());

        $text = is_bn()
            ? $date->translatedFormat($format ?? 'j F, Y')
            : $date->translatedFormat($format ?? 'F j, Y');

        return to_locale_digits($text);
    }
}

if (! function_exists('stars')) {
    /** "★★★★☆" for a 1-5 rating. */
    function stars(int $rating): string
    {
        $rating = max(0, min(5, $rating));

        return str_repeat('★', $rating).str_repeat('☆', 5 - $rating);
    }
}

if (! function_exists('initials')) {
    /** Up to two initials of a name (works for Latin and Bangla). */
    function initials(string $name): string
    {
        $letters = collect(preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY))
            ->take(2)
            ->map(fn (string $word) => mb_substr($word, 0, 1));

        return mb_strtoupper($letters->implode(''));
    }
}

if (! function_exists('placeholder_image')) {
    /** Stand-in photo until real uploads arrive (Phase 8). */
    function placeholder_image(string $seed, int $width, int $height): string
    {
        return "https://picsum.photos/seed/{$seed}/{$width}/{$height}";
    }
}
