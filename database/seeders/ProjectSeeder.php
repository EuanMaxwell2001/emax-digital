<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Support\ResponsiveImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * The three case studies from the Astro site (src/data/site.ts), with their images.
 * Re-running it replaces projects with the same slug.
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->projects() as $i => $data) {
            $details = $data['details'];
            unset($data['details']);

            Project::where('slug', $data['slug'])->get()->each->delete();

            $data['cover_path'] = $this->image($data['cover_path']);
            $data['cover_meta'] = ResponsiveImages::generate($data['cover_path']);
            $project = Project::create($data + ['sort_order' => $i + 1, 'is_published' => true]);

            foreach ($details as $j => $d) {
                $d['image_path'] = $this->image($d['image_path']);
                $d['image_meta'] = ResponsiveImages::generate($d['image_path']);
                $project->details()->create($d + ['sort_order' => $j + 1]);
            }
            $this->command?->info("Seeded {$project->title}");
        }
    }

    /** Copy a seed image onto the public disk, as an upload would be. */
    private function image(string $file): string
    {
        $path = "projects/{$file}";
        Storage::disk('public')->put($path, file_get_contents(__DIR__."/images/{$file}"));

        return $path;
    }

    private function projects(): array
    {
        return [
            [
                'slug' => 'print-vision',
                'title' => 'Print Vision',
                'sector' => 'Edinburgh printers',
                'services' => ['Website', 'Logo', 'Hosting'],
                'year' => 2026,
                'summary' => 'A poster-loud website and a new logo for an Edinburgh print shop that has been putting ink on paper since 2004.',
                'description' => 'Print Vision case study: a poster-loud website, new logo and hosting for an Edinburgh printer, set in the cyan, magenta and yellow of a print run.',
                'cover_path' => 'print-vision.jpg',
                'cover_alt' => 'The Print Vision homepage: a huge headline reading “We put ink on things that matter” in black, cyan and magenta.',
                'context' => 'Print Vision has twenty years in the trade: posters, banners, canvas, stationery and pop-up displays, for blue-chip firms and bedroom businesses alike. Their old site did not look like a company that prints big, bold things.',
                'idea' => 'Make the website look like the work. Condensed poster type at full size, the cyan, magenta and yellow of a print run as the only colours, and a running ticker of everything they print.',
                'details' => [
                    ['image_path' => 'print-vision-headline.jpg', 'alt' => 'Close-up of the headline set in black, cyan and magenta condensed capitals.', 'caption' => 'The headline is set in the colours of the inks themselves.', 'shape' => 'wide'],
                    ['image_path' => 'print-vision-ticker.jpg', 'alt' => 'A black strip listing banners, canvas prints, pop-up displays, letterheads and annual reports.', 'caption' => 'A ticker of what they print, so nobody has to dig for it.', 'shape' => 'strip'],
                    ['image_path' => 'print-vision-intro.jpg', 'alt' => 'The intro paragraph beside two buttons: Get a quote and See the work.', 'caption' => 'Plain words about the kit and the people, then two clear next steps.', 'shape' => 'strip'],
                ],
                'outcome' => 'Launched in 2026 with a new logo, and hosted by EMAX. The phone number sits in the top corner of every page, because most print jobs still start with a call.',
            ],
            [
                'slug' => 'fran-jones',
                'title' => 'Fran Jones',
                'sector' => 'Massage therapist',
                'services' => ['Website', 'Logo', 'Hosting'],
                'year' => null,
                'summary' => 'A calm, booking-first website and logo for a massage therapist, so clients can see the treatments and book in a couple of taps.',
                'description' => 'Fran Jones Massage Therapist case study: a calm, booking-first website, logo and hosting so clients can read about treatments and book from a phone.',
                'cover_path' => 'fran-jones.jpg',
                'cover_alt' => 'Fran Jones Massage Therapist homepage with the headline “Restore your health”, massage photography and treatment cards.',
                'context' => 'Fran works one to one with people in pain, from desk-bound backs to athletes in training. Most clients find her on their phone, often while they are sore and short on patience.',
                'idea' => 'Keep it light and clear. Soft blues, real hands-on photography, every treatment explained in plain words, and a Book now button that is always in reach.',
                'details' => [
                    ['image_path' => 'fran-jones-hero.jpg', 'alt' => 'The homepage hero: headline, short introduction, two buttons and three massage photographs.', 'caption' => 'A first screen that says what Fran does and how to book, without scrolling.', 'shape' => 'wide'],
                    ['image_path' => 'fran-jones-treatments.jpg', 'alt' => 'Three treatment cards: sports and remedial, Swedish, and Indian head massage.', 'caption' => 'Each treatment explained the way Fran would explain it in person.', 'shape' => 'wide'],
                    ['image_path' => 'fran-jones-review.jpg', 'alt' => 'A client review praising Fran’s massage, signed Susie Maxwell.', 'caption' => 'Reviews from real clients, given room to breathe.', 'shape' => 'small'],
                ],
                'outcome' => 'Live with a new logo and EMAX hosting, with small changes to treatments and prices covered by the monthly plan.',
            ],
            [
                'slug' => 'esk-vet',
                'title' => 'Esk Vet Consultants',
                'sector' => 'Equine veterinary consultancy',
                'services' => ['Website', 'Hosting'],
                'year' => null,
                'summary' => 'A website for a specialist equine vet consultancy covering Scotland and Northern England, built to feel as assured as the surgeons behind it.',
                'description' => 'Esk Vet Consultants case study: a confident website and hosting for a specialist equine vet consultancy covering Scotland and Northern England.',
                'cover_path' => 'esk-vet.jpg',
                'cover_alt' => 'Esk Vet Consultants homepage in navy and gold: “Elite equine surgical excellence” beside a photo of racehorses jumping a fence.',
                'context' => 'Esk Vet Consultants bring advanced diagnostics and surgery to equine athletes, at the yard. Their clients are trainers and owners trusting them with very valuable horses, and the old web presence did not match that level of care.',
                'idea' => 'Build on the navy and gold of their existing logo, use big confident type, and lead with racing photography that shows exactly who the work is for.',
                'details' => [
                    ['image_path' => 'esk-vet-headline.jpg', 'alt' => 'The headline “Elite equine surgical excellence” in white and gold on navy, with two buttons.', 'caption' => 'Navy and gold taken straight from the existing logo.', 'shape' => 'wide'],
                    ['image_path' => 'esk-vet-photo.jpg', 'alt' => 'Racehorses and jockeys clearing a fence, framed with a gold offset edge.', 'caption' => 'Photography that shows the clients, not the clinic.', 'shape' => 'square'],
                    ['image_path' => 'esk-vet-services.jpg', 'alt' => 'The services introduction: “Our services” with a short summary of diagnostic and surgical work.', 'caption' => 'Services introduced in a sentence a trainer can scan at the yard.', 'shape' => 'strip'],
                ],
                'outcome' => 'Live and hosted by EMAX, with backups and security updates running in the background so the team can get on with their day.',
            ],
        ];
    }
}
