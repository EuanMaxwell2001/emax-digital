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
Web app `~/webapps/emax-digital` on 178.62.53.110 (user `runcloud`), a git checkout of `main`.
The server's default `php` is 8.2, so always call PHP 8.4 by its full path:

    PHP=/RunCloud/Packages/php84rc/bin/php
    cd ~/webapps/emax-digital
    git pull
    $PHP /usr/sbin/composer install --no-dev --optimize-autoloader
    source ~/.nvm/nvm.sh && npm ci && npm run build      # Node 20.19 is fine
    $PHP artisan migrate --force
    $PHP artisan storage:link                            # already exists; harmless
    $PHP artisan optimize
    $PHP artisan filament:optimize

First deploy only: `ADMIN_EMAIL=you@example.com $PHP artisan db:seed --force` seeds the three
projects and prints an admin password. Re-running it resets those three projects.

`.env` on the server: `MAIL_MAILER=log` means emails are only written to the log. For the contact
form to email you, set real SMTP `MAIL_*` details. `CONTACT_TO` defaults to hello@emaxdigital.co.uk.

RunCloud's PHP hardening ("disable_functions" in the web app's PHP settings) blocks `tmpfile`,
`ignore_user_abort` and `set_time_limit`. Livewire needs `tmpfile` for uploads in the admin and
`ignore_user_abort` for upload previews, so remove those two from the list in the RunCloud panel.
(Livewire's script is published to `public/vendor/livewire` on `composer install`, so the admin
login works either way.) Pushing to `main` makes RunCloud `git pull` only; run the steps above.

Images: RunCloud's PHP (GD) writes WebP but not AVIF, so the site serves WebP and JPEG there and
AVIF only where PHP supports it. No queue worker or cron is needed: image sizes and emails run
after the response is sent.

`/robots.txt` only allows indexing when `APP_ENV=production`, so a staging copy stays out of Google.
