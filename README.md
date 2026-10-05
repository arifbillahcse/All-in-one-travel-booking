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
| 5 | Every page rendered from the database | done |
| 6-10 | Booking and contact, admin, media/SEO, tests, deploy | next: Phase 6 |

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
- `public/assets/js/`: small browser scripts: menu, theme, reveal, forms and WhatsApp hand-off (`main.js`), plan estimate (`packages.js`), photo lightbox (`destination.js`), review form (`reviews.js`), article progress bar (`blog-post.js`). `i18n/` only holds the messages those scripts need.
- `resources/views/pages/*.blade.php`: one Blade view per page (home, packages, reviews, contact, why-us, blog, blog-post, destination). Page scripts are pushed to the layout stacks `scripts-data`, `scripts-i18n` and `scripts`.
- `routes/web.php` and `app/Http/Controllers/PageController.php`: routes `/`, `/packages`, `/why-us`, `/reviews`, `/blog`, `/blog/{slug}`, `/contact`, `/destinations/{slug}`. Unknown slugs return 404.
- Pages are rendered from the database by controllers in `app/Http/Controllers` and the partials in `resources/views/partials/cards`. Filters, sorting, search and "show more" on Reviews and Blog are plain query strings (`?destination=`, `?sort=`, `?category=`, `?q=`, `?show=`), so they work without JavaScript and can be linked.

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
- Database content is translated per column (see Database). The few messages that browser scripts show (form errors,
  estimate) come from `public/assets/js/i18n/bn.js` through `TO.t()`; add a key there when a script needs a new message.
