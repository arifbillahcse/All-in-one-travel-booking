# TravelOrio

Bilingual (English / Bangla) travel-booking website for six Bangladesh destinations,
being migrated from a static HTML/CSS/JS site to Laravel 11.

## Status

| Phase | Scope | State |
|---|---|---|
| 1 | Laravel foundation, Blade layout, assets | done |
| 2 | Pages converted to Blade | done |
| 3 | Database, models, seeders | next |
| 4-10 | Bilingual routing, dynamic pages, booking, admin, media/SEO, tests, deploy | planned |

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
- `resources/views/pages/*.blade.php`: one Blade view per page (home, packages, reviews, contact, why-us, blog, blog-post, destination). Page scripts are pushed to the layout stacks `scripts-data`, `scripts-i18n` and `scripts`.
- `routes/web.php` and `app/Http/Controllers/PageController.php`: routes `/`, `/packages`, `/why-us`, `/reviews`, `/blog`, `/blog/{slug}`, `/contact`, `/destinations/{slug}`. Unknown slugs return 404.
- Page content is still rendered by the legacy scripts from `data.js` / `blog-core.js` until Phases 3-5 move it to the database.

## Tests

```bash
php artisan test
```
