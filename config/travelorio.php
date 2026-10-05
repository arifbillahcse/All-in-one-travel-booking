<?php

/*
|--------------------------------------------------------------------------
| TravelOrio site settings
|--------------------------------------------------------------------------
| Single source of truth for brand details. In Phase 7 the admin panel will
| override these values from the `settings` table via the site() helper.
*/

return [
    // Languages the site is published in; the first is the default.
    'locales' => ['en' => 'English', 'bn' => 'বাংলা'],

    'name'     => 'TravelOrio',
    'tagline'  => 'Six Places. One Unforgettable Bangladesh.',
    'email'    => env('TRAVELORIO_EMAIL', 'hello@travelorio.com'),
    'phone'         => env('TRAVELORIO_PHONE', '+8801779440297'),
    'phone_display' => env('TRAVELORIO_PHONE_DISPLAY', '+880 1779-440297'),
    'whatsapp' => env('TRAVELORIO_WHATSAPP', '8801779440297'),

    'social' => [
        'facebook'  => env('TRAVELORIO_FACEBOOK', '#'),
        'instagram' => env('TRAVELORIO_INSTAGRAM', '#'),
        'youtube'   => env('TRAVELORIO_YOUTUBE', '#'),
    ],

    // Destination slugs and display names (replaced by the database in Phase 3).
    'destinations' => [
        'coxs-bazar'   => "Cox's Bazar",
        'sundarbans'   => 'Sundarbans',
        'sylhet'       => 'Sylhet',
        'bandarban'    => 'Bandarban',
        'saint-martin' => "Saint Martin's Island",
        'kuakata'      => 'Kuakata',
    ],

    // Blog slugs until articles move to the database (Phase 3).
    'blog_slugs' => [
        'cox-bazar-3-days',
        'sundarbans-what-to-expect',
        'best-time-to-visit-bangladesh',
        'saint-martin-ship-rules-packing',
        'bandarban-first-timers',
        'sylhet-tea-and-food',
    ],
];
