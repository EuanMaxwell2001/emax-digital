# EMAX Digital website (Laravel)

Portfolio/agency site for EMAX Digital, the owner's one-person UK freelance web studio.
A port of the finished Astro site in `../emax-new` (kept untouched as the reference).

**The owner loves the design. Do not redesign anything.** Design system and rationale:
`../emax-new/DESIGN.md`. Copy: "we" voice (owner prefers not to stress it being one person), no em dashes in visible text.

## Stack
- Laravel 13, PHP 8.4 (Docker stack `../../stacks/emax`, http://emax.test), MySQL (shared server).
- Filament 5 admin at `/admin`: **Projects** (with "Up close" detail images) and **Messages**
  (contact form, read and delete only). The homepage is hardcoded on purpose.
- Front end: Blade + Vite. `resources/css/{tokens,base,site}.css`, `resources/js/engine/*` and
  `resources/js/motion/*` are copied unchanged from the Astro site. `resources/js/engine/swup.ts`
  replaces Astro's ClientRouter adapter (leave / cleanup / enter mapped onto Swup hooks; Swup
  containers are the header, menu, main and footer, so `#curtain` and `#cursor` persist).
- Built CSS is inlined into `<head>` (as Astro did) unless the Vite dev server is running.

## Where things live
- Homepage copy, services, process, statement, about: `config/site.php`.
- Pages: `resources/views/pages/{home,project}.blade.php`, `errors/404.blade.php`;
  layout and parts in `resources/views/components/`.
- Contact form: `components/contact.blade.php`, `resources/js/contact.ts`,
  `resources/css/contact-form.css`, `ContactController` (honeypot, 3 s time trap, rate limit
  `contact` in `AppServiceProvider`, emails `CONTACT_TO` after the response).
- Images: `<x-picture>` (`app/View/Components/Picture.php`) writes the same `<picture>` markup as
  Astro (AVIF where the server supports it, WebP, JPEG). Sizes are made by `App\Support\ResponsiveImages` into
  `variants/` on the same disk, after the response when an image is uploaded
  (`HasResponsiveImages` trait). `php artisan images:generate --projects [--missing]` / `--static`.
- The About portrait and its sizes live in `public/images/` (committed).

## Commands (run inside the container)
    docker exec -u www-data -w /var/www/html site-emax-php-1 php artisan test
    docker exec -u www-data -w /var/www/html site-emax-php-1 php artisan db:seed --class=ProjectSeeder
    npm run build        # on the host; npm run dev for hot reload

## Gotchas
- `sites db emax` for the database. Passwords come from the container environment, not `.env`.
- Don't install Laravel Boost or a host PHP: everything PHP runs in the container.
