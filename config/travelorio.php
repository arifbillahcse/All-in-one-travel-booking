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

    'name' => 'TravelOrio',
    'tagline' => 'Six Places. One Unforgettable Bangladesh.',
    'email' => env('TRAVELORIO_EMAIL', 'hello@travelorio.com'),
    // Where new inquiries and reviews are emailed (defaults to the public email).
    'notify_email' => env('TRAVELORIO_NOTIFY_EMAIL'),
    'phone' => env('TRAVELORIO_PHONE', '+8801779440297'),
    'phone_display' => env('TRAVELORIO_PHONE_DISPLAY', '+880 1779-440297'),
    'whatsapp' => env('TRAVELORIO_WHATSAPP', '8801779440297'),

    'social' => [
        'facebook' => env('TRAVELORIO_FACEBOOK', '#'),
        'instagram' => env('TRAVELORIO_INSTAGRAM', '#'),
        'youtube' => env('TRAVELORIO_YOUTUBE', '#'),
    ],

    // Staging copies must not appear in search engines.
    'noindex' => (bool) env('SITE_NOINDEX', false),

    'backup' => [
        'path' => env('BACKUP_PATH', storage_path('app/backups')),
        'keep' => (int) env('BACKUP_KEEP', 14),
        'disk' => env('BACKUP_DISK'),   // optional second copy on another filesystem disk
    ],
];
