# HealthData Talent UK

Specialist recruitment platform for health data, health informatics and digital health, plus remote NGO work for doctors. Built with Laravel 12.

## Run it locally

```bash
composer install
cp .env.example .env        # then: php artisan key:generate
php artisan migrate --seed  # prints the admin password once
php artisan serve
```

Open http://localhost:8000. Staff sign in at http://localhost:8000/admin/login.

The seeder creates one admin account (`ADMIN_EMAIL` in `.env`). Set `ADMIN_PASSWORD` before seeding, or leave it empty and copy the random password the seeder prints. While `ADMIN_PASSWORD` is set, every seed run resets the admin password to it, so changing it and re-seeding recovers a lost login. At first sign-in you enrol an authenticator app.

There is no front-end build step. Styles and scripts are plain files in `public/css/site.css` and `public/js/site.js`.

SQLite is the default database. To use MySQL (XAMPP) or PostgreSQL, change the `DB_*` lines in `.env` and run `php artisan migrate --seed` again.

## What's in it

| Public site | Admin (`/admin`) |
| --- | --- |
| Home, About, Doctors & NGOs | Dashboard: candidates, employers, live jobs, applications, placements |
| Employer registration, vacancy submission | Approve or decline employers |
| Candidate registration with CV upload (data and doctor tracks) | Candidate profiles, notes, status, CV download |
| Jobs board with search and filters, job pages | Approve, edit, close and create vacancies, with suggested candidate matches |
| Insights articles | Application pipeline: new → shortlisted → interview → offer → placed |
| Contact form, privacy notice, terms | Enquiries, Insights editor, audit log |

Submitted vacancies are always `pending` and stay off the jobs board until a member of staff approves them.

## Security and data protection

- **Two-factor sign-in** (TOTP) is required for every staff account. `HDT_REQUIRE_2FA=false` turns it off for local development only.
- **Roles**: `recruiter` can work with records; only `admin` can delete records and read the audit log.
- **Login lockout**: five failed attempts per email and IP locks sign-in for 15 minutes.
- **CVs** are stored in `storage/app/private/cvs` under random names, outside the web root. They are served only through the authenticated admin download route. Uploads are limited to PDF/Word and 5 MB, and the file's leading bytes must match its type.
- **Malware scanning**: set `HDT_CLAMAV_PATH` to a `clamscan` binary to scan each upload. Without it, CVs are marked "not malware-scanned" in the admin.
- **Encrypted at rest**: phone numbers, professional registration numbers and 2FA secrets are encrypted with the app key. Sessions are encrypted.
- **Headers**: strict Content-Security-Policy (no inline scripts or styles), HSTS over HTTPS, frame, referrer and permissions policies. Admin pages are `no-store` and `noindex`.
- **Forms**: CSRF tokens, server-side validation, a honeypot field and rate limiting (10 posts a minute per IP).
- **Consent records**: each acceptance of the privacy notice is stored with its version, time, IP and browser.
- **Audit log**: sign-ins, failed sign-ins, profile views, CV downloads, approvals, stage changes and deletions.
- **Retention**: `php artisan hdt:prune-candidates` deletes profiles and CV files with no contact for `HDT_RETENTION_MONTHS` (24). It is scheduled daily; `--dry-run` shows the count first.

## Commands

```bash
php artisan hdt:user jo@example.com "Jo Bloggs" --role=recruiter   # create staff login / reset password
php artisan hdt:reset-2fa jo@example.com                           # lost authenticator device
php artisan hdt:prune-candidates --dry-run                         # retention check
php artisan test
```

## Deploy to Render

The repository includes a `Dockerfile` and a `render.yaml` blueprint (web service plus PostgreSQL).

1. Sign in at https://render.com with GitHub and choose **New > Blueprint**.
2. Pick this repository and apply. Render builds the image, runs migrations and seeds the admin login.
3. The admin password is the generated `ADMIN_PASSWORD` under the service's **Environment** tab. To reset a lost admin login, change that value and redeploy.

On the free plan the server's disk is wiped on every deploy and restart, so **uploaded CVs do not persist**, and the free database is time-limited. Use a paid plan with the disk in `render.yaml` uncommented before collecting real candidate data.

## Before going live

1. `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, and serve over HTTPS only.
2. Point the web server at the `public/` folder, never the project root.
3. Add a cron entry for the scheduler: `* * * * * php /path/to/artisan schedule:run`.
4. Set up real mail (`MAIL_*`) and install ClamAV (`HDT_CLAMAV_PATH`).
5. Back up the database and `storage/app/private`, and test a restore. Keep `APP_KEY` safe and backed up separately: encrypted fields cannot be read without it.
6. Fill in the highlighted placeholders in the privacy notice, terms and footer (company number, address, ICO number), and have the legal text reviewed.
7. Remove the example job listings from Admin → Jobs once real vacancies are live.
