# TravelOrio

Bilingual (English / Bangla) travel-booking website for six Bangladesh destinations,
being migrated from a static HTML/CSS/JS site to Laravel 11.

## Status

| Phase | Scope | State |
|---|---|---|
| 1 | Laravel foundation, Blade layout, assets | done |
| 2 | Pages converted to Blade | done |
| 3 | Database, models, seeders | done |
| 4 | Bilingual routing: `/bn/...`, `lang/bn.json`, hreflang | done |
| 5-10 | Dynamic pages, booking, admin, media/SEO, tests, deploy | next: Phase 5 |

The original static site is kept untouched in `static-backup/` as the visual reference.

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
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

## Database

Content tables use `spatie/laravel-translatable`: a translated column stores `{"en": ..., "bn": ...}`
and reads return the current locale, falling back to English.

| Table | Model | Notes |
|---|---|---|
| `destinations` | `Destination` | itinerary, FAQ, seasons, etc. are JSON; `related` lists slugs |
| `packages`, `addons` | `Package`, `Addon` | plan cards, comparison table and extras |
| `reviews` | `Review` | belongs to a destination; `is_approved` hides a review |
| `post_categories`, `posts` | `PostCategory`, `Post` | body is an ordered JSON list of blocks (`p`, `h2`, `ul`, `tip`) |
| `inquiries` | `Inquiry` | booking and contact leads with a status workflow |
| `settings` | `Setting` | admin overrides for `config/travelorio.php` via `site()` |

`php artisan migrate:fresh --seed` rebuilds everything from `database/seeders/data/*.json`
(exported from the old static content by `node tools/export-legacy-content.cjs`).
Seeders update by slug, so re-running them never duplicates rows, and a missing Bangla
string stops the seed with an error.

## Languages

English lives at `/...` and Bangla at `/bn/...`; the URL decides the language (`App\Http\Middleware\SetLocale`),
so every page has its own indexable address and `hreflang` links.

- Write UI text in Blade in English: `{{ __('Book Now') }}`. The English text is the key, and `lang/bn.json` holds the Bangla.
  A missing translation shows English. `php artisan test` fails if a view uses a string that is not in `lang/bn.json`.
- Don't add keys that are only digits (Laravel renumbers them). Use `to_locale_digits('3')` instead.
- Helpers: `lroute('packages')` / `lurl('blog')` (links in the current language), `alternate_url('bn')` (language switch),
  `t('{name} Tour Packages', ['name' => $n])`, `format_money()`, `format_number()`, `format_date()`, `to_locale_digits()`.
- Database content is translated per column (see Database). Sections still built by the old browser scripts
  (`public/assets/js/*.js`) use `i18n/core.js` with the page language from `<html lang>`; Phase 5 removes that.
