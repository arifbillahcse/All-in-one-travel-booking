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
| 6 | Booking, contact and review forms saved to the database | done |
| 7 | Admin panel (Filament) | done |
| 8 | Photo uploads, SEO and speed | done |
| 9 | Security headers, browser tests, accessibility, CI | done |
| 10 | Deployment, backups, handover | done |

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
php artisan storage:link   # makes uploaded photos reachable at /storage
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

## Forms and leads

Booking (home, packages, destination), contact and review forms post to Laravel
(`InquiryController`, `ReviewController@store`) at `/inquiries/booking`, `/inquiries/contact` and `/reviews`
(plus the `/bn/...` twins).

1. The request is validated on the server (`app/Http/Requests`); messages are translated.
2. A booking or contact message is saved in `inquiries` (status `new`, language, IP, plan, destination, estimate).
   The estimate is always calculated on the server from the plan price. A review is saved with `is_approved = false`.
3. The team gets an email (`App\Mail\InquiryReceived`, `ReviewSubmitted`) at `TRAVELORIO_NOTIFY_EMAIL`
   (defaults to `TRAVELORIO_EMAIL`). A mail failure is logged and never blocks the visitor.
4. The visitor is sent to WhatsApp with the message prefilled in their language (`App\Services\WhatsAppMessage`).
   With JavaScript the page opens WhatsApp in a new tab; without it the form redirects.

Protection: CSRF token, hidden honeypot field (`website`; spam gets a normal-looking reply but is not saved),
and the `inquiries` rate limit (5 per minute and 40 per day per IP, see `AppServiceProvider`).

Set `MAIL_MAILER` and the `MAIL_*` values in `.env` for real email; the default `log` writes mails to `storage/logs`.
`APP_TIMEZONE` is `Asia/Dhaka`, which decides what "today" means for travel dates.
After adding a message that scripts show, run `node tools/build-js-messages.cjs` to refresh `public/assets/js/i18n/bn.js`.

## Admin panel

Filament 3 at `/admin` (login at `/admin/login`). Create the first owner from the terminal:

```bash
php artisan travelorio:admin you@example.com --name="Your Name"      # asks for the password
php artisan travelorio:admin editor@example.com --role=editor
```

| Role | Can do |
|---|---|
| owner | everything, including Team and Site settings |
| editor | destinations, packages, add-ons, blog, reviews and inquiries |

- **Content** (Destinations, Packages, Add-ons, Blog posts, Blog categories) has an English/Bangla switch at the top of every form.
  Each language is saved separately; switching and saving keeps both (see `app/Filament/Concerns/SavesTranslatedForms.php`).
- **Inquiries** show new leads (badge in the menu), with filters, status, notes, WhatsApp link and CSV export.
- **Reviews** submitted by visitors wait here; use Approve or Hide.
- **Blog posts** are built from blocks (paragraph, heading, bullet list, tip). A future publish date schedules an article; turning off Published keeps a draft.
- **Site settings** (owner) override the phone, email, WhatsApp number and social links from `config/travelorio.php`; leave a field empty to go back to the default.

Admin assets are generated by `php artisan filament:upgrade` (runs on `composer install`). Use long passwords for admin accounts.

## Photos, SEO and speed

**Photos.** Upload them in the admin (destination hero, card and gallery; article cover). Each upload is converted to
WebP in several widths (`HasImages`, `app/Models/Concerns`) and pages use `srcset`, so phones download small files.
Without an upload the placeholder photo from the seeders is shown. Set `APP_URL` to the real site address
(`https://example.com`): photo links and the share image are built from it. Keep PHP's `upload_max_filesize` and
`post_max_size` at 12M or more (the admin allows 10 MB per photo). Conversions run on the queue named in `QUEUE_CONNECTION`
(`sync` converts right away; on a server with a queue worker, run it).

**SEO.**
- Every page has a title, description, canonical address, `hreflang` (en, bn, x-default), Open Graph and Twitter tags.
  The share image is the destination hero or article cover, otherwise `public/images/og-default.png`.
- Search results and filtered lists are `noindex`; their canonical address is the clean page.
- JSON-LD: `TravelAgency` and `WebSite` everywhere; `TouristTrip`, `FAQPage` and breadcrumbs on destinations;
  `BlogPosting` and breadcrumbs on articles (`app/Support/StructuredData.php`). Text is escaped so admin content can't break the script tag.
- `/sitemap.xml` lists every published page in both languages with alternates; `/robots.txt` blocks `/admin` and points to the sitemap.
  Submit the sitemap in Google Search Console and Bing Webmaster Tools after launch.

**Speed.**
- Fonts load without blocking the first paint, scripts carry `?v=<file time>` so browsers refetch after a change, images declare their size (no layout shift).
- `ContentCache` caches the menu list and the sitemap; saving or deleting any content bumps a version number, so changes show at once.
- A test fails if a page's database queries grow with the amount of content (N+1 guard).
- Pages contain a per-visitor form token, so don't cache whole pages at a CDN; cache `/assets`, `/build` and `/storage` instead.

## Tests

```bash
php artisan test            # 128 PHP tests: pages, translations, database, forms, admin, media, SEO, security
vendor/bin/pint --test      # code style (run `vendor/bin/pint` to fix it)
npm run build && npm run test:e2e   # 65 browser tests in Chromium (needs: npm i, npx playwright install chromium)
```

The browser tests (`tests/e2e`) start their own PHP server with a throw-away SQLite database, then check:
every public page in English and Bangla on a desktop and a phone (no console errors, no CSP violations, one `h1`,
labels, `alt` text, no horizontal scroll); every internal link; the language switch, dark mode and mobile menu;
booking, contact and review forms; reviews and blog filters; the gallery lightbox; admin login and an edit that shows on the site;
and accessibility (text contrast in light and dark mode, keyboard focus, reduced motion, tap-target size).
`.github/workflows/tests.yml` runs all of it, plus `composer audit`, on every push.

## Security

- **Headers** (`SecurityHeaders` middleware): on the public site a Content-Security-Policy where scripts need a per-request nonce
  (no inline or eval), no plugins, no framing, forms may only post to this site or WhatsApp; `nosniff`, `Referrer-Policy`,
  `Permissions-Policy`, and HSTS over HTTPS in production. `/admin` is not indexed and may only be framed by itself;
  it has no CSP because Filament needs inline scripts.
- **Inline scripts** in a view must carry `nonce="{{ csp_nonce() }}"`; don't add `onclick=`-style handlers or inline `<style>` blocks.
- **Admin text**: HTML is escaped everywhere. The only exception is the "Getting there" lines, which keep `<strong>`/`<em>` and nothing else (`rich_text()`).
  JSON-LD is encoded so it cannot close its `<script>` tag.
- **Forms**: server validation, CSRF, honeypot, rate limits; names lose line breaks (no email-header injection);
  fields a visitor sends that the form doesn't have are ignored; the estimate is computed on the server.
- **Admin**: login is throttled; roles; uploads limited to JPG, PNG and WebP; CSV export neutralises spreadsheet formulas.
- Behind Cloudflare or a load balancer set `TRUSTED_PROXIES=*` so HTTPS and visitor IPs are read correctly.
- Run `composer audit` and `npm audit` regularly.

**Accessibility**: colours were checked against WCAG AA. The call-to-action coral is now a deeper tone (`--accent-strong`) so white text reaches 5:1;
the bright coral stays for decoration and dark backgrounds.

## Deployment and operations

- Server guide: [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md); owner's guide: [docs/HANDOVER.md](docs/HANDOVER.md).
- Config templates in `deploy/` (Nginx, Supervisor queue worker, cron, `deploy.sh`) and `.env.production.example`.
- `php artisan travelorio:preflight [--strict]` checks debug off, https URL, real mail, queue, logs, PHP limits, build and storage link.
- `php artisan travelorio:backup` / `travelorio:restore [file]`: DB + photos in one checksummed zip; nightly at 02:30 via the scheduler; optional off-server copy with `BACKUP_DISK`.
- `SITE_NOINDEX=true` hides a staging site from search engines.
- Old static URLs (`/packages.html`, `/destination.html?place=…`, `?lang=bn`) 301-redirect to the new pages.
