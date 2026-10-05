<?php

/*
|--------------------------------------------------------------------------
| TravelOrio site settings
|--------------------------------------------------------------------------
| Single source of truth for brand details. In Phase 7 the admin panel will
| override these values from the `settings` table via the site() helper.
*/

return [
    'name'     => 'TravelOrio',
    'tagline'  => 'Six Places. One Unforgettable Bangladesh.',
    'email'    => env('TRAVELORIO_EMAIL', 'hello@travelorio.com'),
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
];
