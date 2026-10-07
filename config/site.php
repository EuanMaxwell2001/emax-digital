<?php

/*
|--------------------------------------------------------------------------
| Site content
|--------------------------------------------------------------------------
|
| Homepage copy lives here until the CMS is added. Each list maps cleanly
| onto a future database table (services, projects, process steps).
|
*/

return [

    'email' => env('CONTACT_EMAIL', 'hello@emaxdigital.co.uk'),

    'description' => 'Websites, branding and hosting for businesses that don\'t want to look like everyone else.',

    'statement' => 'Your website should work as hard as you do. Sharp design, fast pages, and a site that\'s looked after long after launch day.',

    'marquee' => ['Web design', 'Development', 'Branding', 'Hosting', 'Apps'],

    'values' => ['Fast', 'Sharp', 'Looked after', 'Built to last'],

    'services' => [
        // 'preview' picks the hover card in resources/views/home/previews/.
        ['preview' => 'websites', 'title' => 'Websites', 'blurb' => 'Custom-built, quick to load, and easy for you to update.'],
        ['preview' => 'branding', 'title' => 'Branding', 'blurb' => 'Logos and brand guides that make you look like you mean it.'],
        ['preview' => 'hosting', 'title' => 'Hosting', 'blurb' => 'Hosted, backed up and kept secure, with small changes included.'],
        ['preview' => 'apps', 'title' => 'Apps', 'blurb' => 'Booking tools, dashboards and mobile apps when a site isn\'t enough.'],
    ],

    // First project is shown as the large feature; the rest go in the grid below.
    // 'image' is a path under public/, e.g. 'images/work/print-vision.jpg'.
    'projects' => [
        ['client' => 'Print Vision', 'type' => 'Website · Logo · Hosting', 'url' => null, 'image' => 'images/work/print-vision.jpg'],
        ['client' => 'Fran Jones Massage Therapist', 'type' => 'Website · Logo · Hosting', 'url' => null, 'image' => 'images/work/fran-jones.jpg'],
        ['client' => 'Esk Vet Consultants', 'type' => 'Website · Hosting', 'url' => null, 'image' => 'images/work/esk-vet.jpg'],
    ],

    'process' => [
        ['title' => 'Chat', 'blurb' => 'We talk about your business. You get a clear plan before anything starts.'],
        ['title' => 'Design', 'blurb' => 'You see the design early and we shape it together until it\'s right.'],
        ['title' => 'Build', 'blurb' => 'Built properly, tested on real phones, handed over with a quick how-to.'],
        ['title' => 'Launch', 'blurb' => 'It goes live, and I keep it fast, safe and up to date afterwards.'],
    ],

];
