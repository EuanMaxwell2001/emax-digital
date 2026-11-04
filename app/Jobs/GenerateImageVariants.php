<?php

namespace App\Jobs;

use App\Support\ResponsiveImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Makes the AVIF/WebP/JPEG sizes for one image attribute of a model and stores the meta.
 * Dispatched after the response (no queue worker needed): the admin save returns at once,
 * and until this finishes the site serves the original upload.
 */
class GenerateImageVariants
{
    use Dispatchable;

    /** @param class-string<Model> $model */
    public function __construct(
        public string $model,
        public int $id,
        public string $pathAttribute,
        public string $metaAttribute,
    ) {}

    public function handle(): void
    {
        $record = $this->model::find($this->id);
        $path = $record?->{$this->pathAttribute};
        if (! $path) {
            return;
        }

        $meta = ResponsiveImages::generate($path);

        // The image may have been replaced while this ran; only store meta for the current one.
        $record->refresh();
        if ($record->{$this->pathAttribute} === $path) {
            $record->{$this->metaAttribute} = $meta;
            $record->saveQuietly();
        }
    }
}
