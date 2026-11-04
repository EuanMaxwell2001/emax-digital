<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ProjectDetail;
use App\Support\ResponsiveImages;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateImages extends Command
{
    protected $signature = 'images:generate
        {--static : Hardcoded images in public/images (commit the results)}
        {--projects : Every project cover and detail image}
        {--missing : With --projects, only images whose sizes are not made yet}';

    protected $description = 'Make the AVIF/WebP/JPEG sizes used by <x-picture>';

    public function handle(): int
    {
        if (! $this->option('static') && ! $this->option('projects')) {
            $this->error('Pass --static and/or --projects.');

            return self::FAILURE;
        }

        if ($this->option('static')) {
            foreach (Storage::disk('static')->files() as $file) {
                if (preg_match('/\.(jpe?g|png|webp)$/i', $file)) {
                    ResponsiveImages::generate($file, 'static');
                    $this->line("static/{$file}");
                }
            }
        }

        if ($this->option('projects')) {
            $missing = (bool) $this->option('missing');
            $run = function ($model, string $path, string $meta) use ($missing) {
                if (! $model->{$path} || ($missing && $model->{$meta})) {
                    return;
                }
                $model->{$meta} = ResponsiveImages::generate($model->{$path});
                $model->saveQuietly();
                $this->line($model->{$path});
            };
            Project::each(fn ($p) => $run($p, 'cover_path', 'cover_meta'));
            ProjectDetail::each(fn ($d) => $run($d, 'image_path', 'image_meta'));
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
