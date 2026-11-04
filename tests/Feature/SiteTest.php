<?php

namespace Tests\Feature;

use App\Mail\ContactReceived;
use App\Models\ContactSubmission;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    private function project(array $attrs = []): Project
    {
        return Project::create($attrs + [
            'title' => 'Print Vision',
            'slug' => 'print-vision',
            'sector' => 'Edinburgh printers',
            'services' => ['Website', 'Logo'],
            'year' => 2026,
            'summary' => 'A poster-loud website.',
            'description' => 'Print Vision case study.',
            'cover_alt' => 'Cover',
            'context' => 'Brief text.',
            'idea' => 'Idea text.',
            'outcome' => 'Result text.',
            'sort_order' => 1,
        ]);
    }

    private function formFields(array $extra = []): array
    {
        return $extra + [
            'name' => 'Test Person',
            'email' => 'test@example.com',
            'message' => 'I need a website for my bakery.',
            'page' => '/work/print-vision',
            'started' => encrypt(now()->subMinute()->getTimestamp()),
        ];
    }

    public function test_home_lists_published_projects_only(): void
    {
        $this->project();
        $this->project(['title' => 'Hidden One', 'slug' => 'hidden', 'is_published' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Websites', false)
            ->assertSee('href="/work/print-vision"', false)
            ->assertDontSee('Hidden One');
    }

    public function test_case_study_page_and_seo(): void
    {
        $this->project();

        $this->get('/work/print-vision')
            ->assertOk()
            ->assertSee('<title>Print Vision case study | EMAX Digital</title>', false)
            ->assertSee('<link rel="canonical" href="'.url('/work/print-vision').'"', false)
            ->assertSee('"@type":"CreativeWork"', false)
            ->assertSee('<dt>Year</dt><dd>2026</dd>', false);
    }

    public function test_unpublished_project_is_404_unless_signed_in(): void
    {
        $this->project(['is_published' => false]);

        $this->get('/work/print-vision')->assertNotFound()->assertSee('make the cut', false);
        $this->actingAs(User::factory()->create())->get('/work/print-vision')->assertOk();
    }

    public function test_old_trailing_slash_urls_redirect(): void
    {
        // The test client trims trailing slashes, so send the raw request through the kernel.
        $response = $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->handle(\Illuminate\Http\Request::create('http://localhost/work/print-vision/?a=1'));

        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('http://localhost/work/print-vision?a=1', $response->headers->get('Location'));
    }

    public function test_sitemap_and_robots(): void
    {
        $this->project();
        $this->project(['slug' => 'hidden', 'is_published' => false]);

        $this->get('/sitemap.xml')->assertOk()
            ->assertSee(url('/work/print-vision'), false)
            ->assertDontSee('/work/hidden', false);
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /'); // not production
        $this->get('/sitemap-index.xml')->assertRedirect('/sitemap.xml');
    }

    public function test_contact_form_saves_and_emails(): void
    {
        Mail::fake();

        $this->postJson('/contact', $this->formFields())->assertOk()->assertJson(['ok' => true]);

        $this->assertDatabaseHas('contact_submissions', ['email' => 'test@example.com', 'page' => '/work/print-vision']);
        Mail::assertSent(ContactReceived::class, fn ($m) => $m->hasTo(config('site.contact_to')) && $m->hasReplyTo('test@example.com'));
    }

    public function test_contact_form_without_js_redirects_back_to_the_form(): void
    {
        Mail::fake();

        $this->post('/contact', $this->formFields())
            ->assertRedirect(url('/work/print-vision').'#contact')
            ->assertSessionHas('contact_sent');
    }

    public function test_contact_form_validates(): void
    {
        $this->postJson('/contact', $this->formFields(['email' => 'nope', 'message' => 'hi']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'message']);
        $this->assertSame(0, ContactSubmission::count());
    }

    public function test_bots_get_a_normal_reply_but_nothing_is_saved(): void
    {
        Mail::fake();

        $this->postJson('/contact', $this->formFields(['website' => 'http://spam.example']))->assertOk();
        $this->postJson('/contact', $this->formFields(['started' => encrypt(now()->getTimestamp())]))->assertOk();
        $this->postJson('/contact', $this->formFields(['started' => 'tampered']))->assertOk();

        $this->assertSame(0, ContactSubmission::count());
        Mail::assertNothingSent();
    }

    public function test_contact_form_ignores_offsite_return_pages(): void
    {
        Mail::fake();

        $this->post('/contact', $this->formFields(['page' => '//evil.example/x']))
            ->assertRedirect(url('/').'#contact');
    }

    public function test_contact_form_is_rate_limited(): void
    {
        Mail::fake();

        foreach (range(1, 5) as $_) {
            $this->postJson('/contact', $this->formFields())->assertOk();
        }
        $this->postJson('/contact', $this->formFields())->assertStatus(429);
    }

    public function test_images_get_sizes_and_are_deleted_with_the_project(): void
    {
        Storage::fake('public');
        $path = UploadedFile::fake()->image('cover.jpg', 1000, 600)->store('projects', 'public');

        $project = $this->project(['cover_path' => $path]);
        $detail = $project->details()->create([
            'image_path' => UploadedFile::fake()->image('d.jpg', 500, 300)->store('projects', 'public'),
            'alt' => 'Detail', 'shape' => 'wide',
        ]);

        // Sizes are made after the response; in tests that happens when the app terminates.
        $this->app->terminate();
        $project->refresh();
        $this->assertSame([320, 480, 640, 960, 1000], $project->cover_meta['widths']);
        Storage::disk('public')->assertExists('variants/'.str_replace('.jpg', '-640.avif', $path));

        $this->get('/work/print-vision')->assertSee('type="image/avif"', false)->assertSee('width="1000" height="600"', false);

        $project->delete();
        Storage::disk('public')->assertMissing($path);
        Storage::disk('public')->assertMissing($detail->image_path);
        Storage::disk('public')->assertMissing('variants/'.str_replace('.jpg', '-640.avif', $path));
    }

    public function test_servers_without_avif_get_webp_and_jpeg_only(): void
    {
        Storage::fake('public');
        \App\Support\ResponsiveImages::$formatsOverride = ['webp', 'jpg']; // like RunCloud's GD

        try {
            $path = UploadedFile::fake()->image('cover.jpg', 800, 500)->store('projects', 'public');
            $project = $this->project(['cover_path' => $path]);
            $this->app->terminate();

            $this->assertSame(['webp', 'jpg'], $project->refresh()->cover_meta['formats']);
            Storage::disk('public')->assertMissing('variants/'.str_replace('.jpg', '-640.avif', $path));
            Storage::disk('public')->assertExists('variants/'.str_replace('.jpg', '-640.webp', $path));
            $this->get('/work/print-vision')
                ->assertDontSee('image/avif', false)
                ->assertSee('type="image/webp"', false);
        } finally {
            \App\Support\ResponsiveImages::$formatsOverride = null;
        }
    }

    public function test_admin_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin/projects')->assertOk();
    }
}
