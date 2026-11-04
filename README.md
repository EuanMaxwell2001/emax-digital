# EMAX Digital website

Laravel 13 + Filament 5. The design, motion and markup are the same as the Astro site it replaced;
projects are edited at `/admin`, and the contact form saves messages there and emails them.

## Local
Runs in the Docker stack `~/Projects/stacks/emax` at http://emax.test (`sites up emax`).

    npm install && npm run build       # or npm run dev while editing CSS/JS
    php artisan migrate --seed         # seeds the three projects; add ADMIN_EMAIL=you@... for a login
    php artisan test

Email goes to Mailpit locally: http://mail.test

## Edit content
- Projects (text, cover, close-up images, order, published): `/admin`.
- Everything on the homepage that is not a project: `config/site.php`.
- New admin login: `php artisan make:filament-user`.

## Deploy (DigitalOcean droplet with RunCloud)
1. Web app: PHP 8.4, public path `/public`. PHP extensions: gd (with AVIF and WebP) or imagick.
   AVIF needs GD built with libavif or ImageMagick with AVIF; check `php -r 'var_dump(function_exists("imageavif"));'`.
2. `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://emaxdigital.co.uk`,
   database, SMTP (`MAIL_*`), `MAIL_FROM_ADDRESS`, `CONTACT_TO`.
3. Deploy script:

       composer install --no-dev --optimize-autoloader
       npm ci && npm run build            # or build locally and upload public/build
       php artisan migrate --force
       php artisan storage:link
       php artisan optimize
       php artisan filament:optimize

4. First deploy only: `php artisan db:seed --force` (with `ADMIN_EMAIL=...`), or upload the local
   database and `storage/app/public`.
5. No queue worker or cron is needed: image sizes and emails run after the response is sent.

`/robots.txt` only allows indexing when `APP_ENV=production`, so a staging copy stays out of Google.
