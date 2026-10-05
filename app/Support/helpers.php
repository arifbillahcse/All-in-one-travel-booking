<?php

if (! function_exists('site')) {
    /**
     * Read a TravelOrio site setting, e.g. site('whatsapp') or site('social.facebook').
     */
    function site(string $key, mixed $default = null): mixed
    {
        return config('travelorio.'.$key, $default);
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
