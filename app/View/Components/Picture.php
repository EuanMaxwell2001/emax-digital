<?php

namespace App\View\Components;

use App\Support\ResponsiveImages;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

/**
 * <picture> with AVIF and WebP sources and a JPEG fallback, the same markup Astro's <Picture> made.
 * Widths larger than the original are dropped and the original width added (as Astro does).
 * If the sizes have not been generated yet, it falls back to the original upload.
 *
 *   <x-picture :src="$p->cover_path" :meta="$p->cover_meta" :alt="$p->cover_alt"
 *              :widths="[640, 960, 1280, 1600]" sizes="..." loading="lazy" />
 */
class Picture extends Component
{
    public array $sources = [];

    public ?string $fallback = null;

    public ?string $fallbackSrcset = null;

    public ?int $width = null;

    public ?int $height = null;

    public function __construct(
        public ?string $src,
        public string $alt = '',
        public ?array $meta = null,
        public array $widths = [640, 960, 1280, 1600],
        public string $sizes = '100vw',
        public string $disk = 'public',
        public string $loading = 'lazy',
        public ?string $fetchpriority = null,
        public string $decoding = 'async',
    ) {
        if (! $src) {
            return;
        }
        $meta ??= ResponsiveImages::inspect($src, $disk);
        $fs = Storage::disk($disk);
        $this->width = $meta['width'] ?? null;
        $this->height = $meta['height'] ?? null;
        $this->fallback = $fs->url($src);

        $available = $meta['widths'] ?? [];
        $use = $this->width ? $this->pick($this->width) : [];
        $use = array_values(array_intersect($use, $available));
        if (! $use) {
            return;
        }

        $set = fn (string $format) => collect($use)
            ->map(fn ($w) => $fs->url(ResponsiveImages::variantPath($src, $w, $format))." {$w}w")
            ->implode(', ');
        $this->sources = [
            ['type' => 'image/avif', 'srcset' => $set('avif')],
            ['type' => 'image/webp', 'srcset' => $set('webp')],
        ];
        $this->fallbackSrcset = $set('jpg');
        $this->fallback = $fs->url(ResponsiveImages::variantPath($src, end($use), 'jpg'));
    }

    /** Requested widths up to the original, plus the original if any requested width is larger. */
    private function pick(int $original): array
    {
        $use = array_filter($this->widths, fn ($w) => $w <= $original);
        if (max($this->widths) > $original) {
            $use[] = min($original, max(ResponsiveImages::WIDTHS));
        }

        return array_values(array_unique($use));
    }

    public function render()
    {
        return view('components.picture');
    }
}
