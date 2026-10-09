# TravelOrio – Deployment guide

Target: one VPS (2 vCPU / 2 GB RAM is plenty), Ubuntu 24.04, Nginx, PHP 8.3-FPM, MySQL 8 (or MariaDB 10.11), Cloudflare in front.

> **Honesty box – what was and was not tested.** The application, backup/restore (SQLite), preflight checks and legacy redirects are covered by automated tests. The Nginx file, Supervisor file, `deploy.sh`, MySQL backup/restore (`mysqldump` path) and `npm run build` could **not** be executed in the build sandbox. Do the *first-deploy drill* (section 8) on a staging copy before pointing the real domain.

## 1. Server packages (root)

```bash
apt update && apt -y upgrade
apt -y install nginx mysql-server git unzip supervisor ufw fail2ban certbot python3-certbot-nginx \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-sqlite3 php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath
# Composer + Node 20+
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt -y install nodejs
```

Firewall + brute-force protection:

```bash
ufw allow OpenSSH && ufw allow 'Nginx Full' && ufw --force enable
systemctl enable --now fail2ban
```
Use SSH keys only (`PasswordAuthentication no`, `PermitRootLogin prohibit-password`).

## 2. PHP settings (`/etc/php/8.3/fpm/conf.d/90-travelorio.ini`)

```ini
upload_max_filesize = 12M
post_max_size = 14M
memory_limit = 256M
max_execution_time = 60
opcache.enable = 1
opcache.validate_timestamps = 0   ; then reload php-fpm after each deploy (deploy.sh -> see step 6)
expose_php = Off
```
`systemctl reload php8.3-fpm`. `travelorio:preflight` warns if any of these are too low.

## 3. Database

```sql
CREATE DATABASE travelorio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'travelorio'@'localhost' IDENTIFIED BY '<long random>';
GRANT ALL ON travelorio.* TO 'travelorio'@'localhost';
```
`mysqldump` (package `mysql-client`/included with server) must be on PATH for backups.

## 4. Code and environment

```bash
adduser --disabled-password deploy && usermod -aG www-data deploy
mkdir -p /var/www/travelorio && chown deploy:www-data /var/www/travelorio
sudo -u deploy git clone <repo-url> /var/www/travelorio/current
cd /var/www/travelorio/current
cp .env.production.example .env && nano .env      # fill DB, APP_URL, mail, business details
composer install --no-dev --optimize-autoloader
php artisan key:generate --force
chgrp -R www-data storage bootstrap/cache && chmod -R ug+rwX storage bootstrap/cache
```
Keep `.env` out of git and back it up separately (it holds `APP_KEY`; losing it logs everyone out and breaks encrypted values).

## 5. Nginx + SSL

```bash
cp deploy/nginx.conf /etc/nginx/sites-available/travelorio && ln -s ../sites-available/travelorio /etc/nginx/sites-enabled/
# first time only: comment out the 443 block (no certificate yet), then
certbot --nginx -d travelorio.com -d www.travelorio.com
nginx -t && systemctl reload nginx
```
**Cloudflare:** DNS `A travelorio.com → server IP` (orange cloud), `CNAME www → travelorio.com`. SSL/TLS mode **Full (strict)**, Always Use HTTPS on, Brotli on. Cache rules: bypass cache for `/admin*` and `/livewire*`. Set `TRUSTED_PROXIES=*` in `.env` and allow only Cloudflare IPs on ports 80/443 if you want to hide the origin.

## 6. First release

```bash
php artisan migrate --force
php artisan db:seed --force                    # the six destinations, packages, add-ons, sample content
php artisan storage:link
php artisan travelorio:admin you@example.com --name="Your Name" --role=owner
php artisan filament:upgrade && php artisan optimize
npm ci && npm run build                        # creates public/build (required: the site refuses to look right without it)
cp deploy/supervisor-queue.conf /etc/supervisor/conf.d/travelorio-queue.conf && supervisorctl reread && supervisorctl update
crontab -u www-data deploy/cron.txt            # nightly backups at 02:30
php artisan travelorio:preflight               # every line should be OK
```
Later releases: `bash deploy/deploy.sh main` (backup → maintenance mode → pull → composer → build → migrate → optimize → queue restart → preflight). If `opcache.validate_timestamps=0`, add `sudo systemctl reload php8.3-fpm` to the end of the script.

## 7. Backups and restore

- Nightly 02:30 via the scheduler: DB dump + all photos + checksum in one zip → `storage/app/backups` (14 kept; `BACKUP_KEEP`).
- **Off-server copy (do this):** set `BACKUP_DISK=s3` (configure the disk in `config/filesystems.php`; Cloudflare R2 / Backblaze B2 / DigitalOcean Spaces all work) or rsync the folder to another machine. A backup on the same disk dies with the server.
- Manual: `php artisan travelorio:backup`. List: `ls -t storage/app/backups`.
- Restore: `php artisan travelorio:restore [file.zip]` – takes a safety backup of the current state first, verifies the checksum, refuses damaged archives.
- `.env` is not in the zip; keep a copy in your password manager.

## 8. First-deploy drill (do once, on staging)

1. Deploy to `staging.travelorio.com` with `SITE_NOINDEX=true` (adds `noindex` header/meta and `Disallow: /`).
2. Open every page in English and `/bn`; submit a test inquiry; confirm it appears in Admin → Inquiries and WhatsApp opens.
3. `php artisan travelorio:backup`, delete a destination in the admin, then `php artisan travelorio:restore` – it must come back with photos.
4. `curl -I https://staging…` → check `Strict-Transport-Security`, `Content-Security-Policy`, `X-Content-Type-Options`.
5. `curl -I https://…/packages.html` → `301` to `/packages` (old links keep working, `?lang=bn` → `/bn/packages`).
6. Only then switch DNS for the real domain and set `SITE_NOINDEX=false`.

## 9. Operations cheat sheet

| Task | Command |
|---|---|
| Health check | `php artisan travelorio:preflight` |
| Logs | `tail -f storage/logs/laravel-$(date +%F).log` |
| Clear caches after editing `.env` | `php artisan optimize:clear && php artisan optimize` |
| Queue stuck | `supervisorctl restart travelorio-queue:*` |
| Reset an admin password | `php artisan travelorio:admin user@example.com --password=...` |
| Maintenance page | `php artisan down` / `php artisan up` |

## CloudPanel variant

Create a **PHP site** (PHP 8.3, root `public`), a MySQL database, and issue the Let's Encrypt certificate in the UI. Paste `deploy/nginx.conf`'s `location` blocks (build/assets/storage caching, dotfile/zip deny, `client_max_body_size 12M`) into the site's *Vhost* editor; CloudPanel supplies the PHP-FPM and SSL parts. Add the cron line and a queue worker via *Cron Jobs* (`php artisan queue:work --stop-when-empty` every minute is an acceptable substitute for Supervisor).
