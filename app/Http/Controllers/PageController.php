<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            'projects' => Project::published()->ordered()->get(),
        ]);
    }

    public function project(string $slug): View
    {
        // Signed-in admins can preview unpublished projects.
        $project = Project::where('slug', $slug)
            ->when(! auth()->check(), fn ($q) => $q->published())
            ->with('details')
            ->firstOrFail();

        return view('pages.project', [
            'p' => $project,
            'next' => $project->next(),
        ]);
    }

    public function sitemap(): Response
    {
        $urls = collect([['loc' => url('/'), 'lastmod' => Project::published()->max('updated_at')]])
            ->concat(Project::published()->ordered()->get()->map(fn (Project $p) => [
                'loc' => url($p->url()),
                'lastmod' => $p->updated_at,
            ]));

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }

    public function robots(): Response
    {
        $body = app()->isProduction()
            ? "User-agent: *\nAllow: /\nDisallow: /admin\n\nSitemap: ".route('sitemap')."\n"
            : "User-agent: *\nDisallow: /\n"; // keep local and staging copies out of search engines

        return response($body)->header('Content-Type', 'text/plain; charset=utf-8');
    }
}
