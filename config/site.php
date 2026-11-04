<?php

// Site copy (was src/data/site.ts in the Astro version). The homepage is hardcoded here;
// projects are edited in the admin (/admin). Voice: "we", no em dashes.

return [
    'name' => 'EMAX Digital',
    'short' => 'EMAX',
    'role' => 'Websites, branding and hosting for small businesses',
    'email' => 'hello@emaxdigital.co.uk',
    // Contact form submissions are emailed here.
    'contact_to' => env('CONTACT_TO', 'hello@emaxdigital.co.uk'),
    'tagline' => 'Websites built to stand out.',
    'hero' => [
        'sub' => 'Fast, custom websites for small businesses. Designed, built and looked after with a real person at the other end of every email.',
        'meta' => 'Web design studio, UK',
    ],
    'description' => 'EMAX Digital is a UK web design studio. We design, build and host fast, custom websites for small businesses. Websites built to stand out.',

    'services' => [
        [
            'name' => 'Websites',
            'line' => 'Custom design, quick on a phone, and easy for you to update.',
            'detail' => 'No templates. Every page designed around what your customers need to find.',
        ],
        [
            'name' => 'Branding',
            'line' => 'Logos and brand guides that hold up from a van door to a favicon.',
            'detail' => 'Colours, type and rules you can hand to a printer or a signwriter.',
        ],
        [
            'name' => 'Hosting',
            'line' => 'Backups, security and small changes included.',
            'detail' => 'One monthly fee. Need a price or a photo changed? Send an email.',
        ],
        [
            'name' => 'Apps',
            'line' => 'Booking tools, dashboards and mobile apps.',
            'detail' => 'For when a website is not enough and a spreadsheet is starting to creak.',
        ],
    ],

    'statement' => [
        'words' => 'Your website should work as hard as you do.',
        'lead' => 'Most small-business websites look like each other. Same template, same stock photos, same slow load on a phone. Yours should be the one people remember.',
        'facts' => [
            ['figure' => '0', 'label' => 'templates. Every site starts from a blank page'],
            ['figure' => '1', 'label' => 'monthly fee for hosting, backups and small changes'],
            ['figure' => '100%', 'label' => 'of the sites we have built are still looked after by us'],
        ],
    ],

    'process' => [
        ['step' => 'Chat', 'text' => 'A call or a coffee. You tell us about the business, we ask a lot of questions, and you get a clear quote and timeline.'],
        ['step' => 'Design', 'text' => 'You see the key pages designed before anything is built. This is where changes happen, while they are quick to make.'],
        ['step' => 'Build', 'text' => 'Hand-built, fast on phones, and set up so you can change text and photos yourself.'],
        ['step' => 'Launch', 'text' => 'We put it live, connect your domain, and stay on for hosting, backups and small changes.'],
    ],

    'about' => [
        'heading' => 'Start to finish, under one roof.',
        'body' => [
            'EMAX Digital designs, builds and hosts websites for small businesses. The design, the build and the hosting all happen in the same place, so the people who made your site are the ones looking after it.',
            'That keeps things quick and keeps the bill sensible. Nothing gets lost in hand-offs, and you always know who to ask.',
        ],
        'caption' => 'Euan Maxwell, EMAX Digital',
    ],
];
