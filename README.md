# TravelOrio

Bilingual (English / Bangla) travel-booking website for six Bangladesh destinations,
being migrated from a static HTML/CSS/JS site to Laravel 11.

## Status

| Phase | Scope | State |
|---|---|---|
| 1 | Laravel foundation, Blade layout, assets | done |
| 2 | Pages converted to Blade | next |
| 3-10 | Database, bilingual routing, dynamic pages, booking, admin, media/SEO, tests, deploy | planned |

The original static site is kept untouched in `static-backup/` as the visual reference.

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build        # or: npm run dev
php artisan serve
```

Requires PHP 8.2+, Composer, Node 20+.

## Structure

- `config/travelorio.php`: brand name, email, WhatsApp number, social links, destination list. Read with `site('whatsapp')`.
- `app/Support/helpers.php`: `site()`, `whatsapp_url()`, `asset_js()`.
- `resources/views/layouts/app.blade.php`: shared layout. Partials: `head`, `navbar`, `footer`, `floats`.
- `resources/css/travelorio.css`: the full "Ocean Luxe" stylesheet, compiled by Vite.
- `public/assets/js/`: legacy scripts from the static site (theme toggle, menu, forms, i18n). Replaced step by step by server-side code in later phases.
- `routes/web.php` and `app/Http/Controllers/PageController.php`: routes (placeholder bodies until Phase 2).

## Tests

```bash
php artisan test
```
