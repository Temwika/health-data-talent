# Deploying to cPanel shared hosting

You need: PHP 8.2 or newer, a MySQL database, and an SSL certificate (cPanel's free AutoSSL is fine).

The rule that matters: **only the `public/` folder may be reachable from the web.** Everything else (`.env`, `storage/` with the CVs, `vendor/`) must sit outside the web root.

## 1. Set the PHP version

cPanel → **Select PHP Version** (or MultiPHP Manager) → choose **8.2 or 8.3** for your domain.
Make sure these extensions are ticked: `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `zip`, `ctype`, `tokenizer`, `xml`, `curl`.

## 2. Create the database

cPanel → **MySQL Databases**:

1. Create a database, e.g. `youruser_hdt`.
2. Create a user with a strong password.
3. Add the user to the database with **All Privileges**.

## 3. Upload the application

cPanel → **File Manager** → go to your home folder (the one that *contains* `public_html`, not inside it).

1. Upload `healthdata-talent-cpanel.zip`.
2. Extract it. You should now have `~/healthdata/` next to `public_html/`.

The zip already includes the `vendor/` folder, so you do not need Composer on the server.

## 4. Configure `.env`

In `~/healthdata`, copy `.env.example` to `.env` (enable "Show hidden files" in File Manager settings) and edit:

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.co.uk
APP_KEY=base64:...             # see below

LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=youruser_hdt
DB_USERNAME=youruser_hdtuser
DB_PASSWORD=your-database-password

SESSION_SECURE_COOKIE=true

ADMIN_EMAIL=you@yourdomain.co.uk
ADMIN_PASSWORD=choose-a-long-password   # used once, when the admin login is created
```

**APP_KEY:** generate a fresh one for the live site. On your own PC, in the project folder, run `php artisan key:generate --show` and paste the whole output (starting `base64:`) into `APP_KEY`. Do not reuse the key from your local `.env`, and never change it after the site holds data.

## 5. Point the domain at `public/`

**Option A (best): change the document root.** For an addon domain or subdomain, cPanel → **Domains** → set the document root to `healthdata/public`. Done.

**Option B: main domain, document root fixed at `public_html`.**

1. Copy everything inside `~/healthdata/public/` (including the hidden `.htaccess`) into `~/public_html/`.
2. Replace `~/public_html/index.php` with `~/healthdata/cpanel/index.php`.

If you extracted the app somewhere other than `~/healthdata`, edit `APP_DIR` at the top of that file.

## 6. Create the tables and the admin login

**If your host gives you Terminal** (cPanel → Terminal) or SSH:

```bash
cd ~/healthdata
php artisan migrate --force --seed
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

If `php` reports a version below 8.2, use the full path, for example `/opt/cpanel/ea-php83/root/usr/bin/php artisan ...`.

**If there is no Terminal**, use cPanel → **Cron Jobs**. Add this as a job set to run every minute, wait two minutes, then **delete it**:

```
cd ~/healthdata && /usr/local/bin/php artisan migrate --force --seed && /usr/local/bin/php artisan config:cache && /usr/local/bin/php artisan route:cache && /usr/local/bin/php artisan view:cache
```

It is safe if it runs more than once: migrations and the seeder only add what is missing.

## 7. Add the permanent cron job

cPanel → **Cron Jobs** → every minute:

```
cd ~/healthdata && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

This runs the daily data-retention clean-up.

## 8. Check it

- `https://yourdomain.co.uk` loads the home page.
- `https://yourdomain.co.uk/.env` returns **404 or 403**. If it shows text, stop: the web root is wrong (go back to step 5).
- `https://yourdomain.co.uk/admin/login` lets you sign in with `ADMIN_EMAIL` / `ADMIN_PASSWORD` and set up two-factor.
- Turn on **Force HTTPS Redirect** in cPanel → Domains.
- Remove `ADMIN_PASSWORD` from `.env`, then re-run `php artisan config:cache`.

## Folder permissions

`storage/` and `bootstrap/cache/` must be writable by PHP (755 for folders is normally enough on cPanel). If you see a blank page or a 500 error, read `~/healthdata/storage/logs/`.

## Updating later

Upload the changed files over the old ones (never overwrite `.env` or `storage/`), then run:

```bash
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## Backups

Back up the MySQL database and `~/healthdata/storage/app/private` (the CVs), and keep a copy of `APP_KEY` somewhere safe. Without the key, encrypted phone and registration numbers cannot be read.
