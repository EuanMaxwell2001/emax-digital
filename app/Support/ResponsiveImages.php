<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Encoders\AvifEncoder;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

/**
 * Generates and finds the resized AVIF / WebP / JPEG copies of an image.
 *
 * An image at `projects/abc.jpg` gets `variants/projects/abc-960.avif` (and .webp, .jpg) on the
 * same disk, for every width in WIDTHS smaller than the original, plus the original width
 * (capped at the largest width). The result is described by a small "meta" array:
 *   ['width' => 1600, 'height' => 1000, 'widths' => [480, ..., 1600], 'formats' => ['avif', 'webp', 'jpg']]
 * Only formats this server's PHP can write are made (RunCloud's GD has WebP but no AVIF);
 * JPEG is always made.
 */
class ResponsiveImages
{
    /** Every width any <x-picture> asks for. */
    public const WIDTHS = [320, 480, 640, 960, 1280, 1400, 1600];

    public const FORMATS = ['avif', 'webp', 'jpg'];

    /** Set in tests to pretend a format is unavailable. */
    public static ?array $formatsOverride = null;

    /** Encoder quality per format (close to Astro/sharp's defaults). */
    private const QUALITY = ['avif' => 55, 'webp' => 80, 'jpg' => 80];

    /** Create every variant of an image and return its meta. */
    public static function generate(string $path, string $disk = 'public'): ?array
    {
        $fs = Storage::disk($disk);
        if (! $fs->exists($path)) {
            return null;
        }

        // RunCloud disables set_time_limit (calling a disabled function is a fatal error in PHP 8).
        if (function_exists('set_time_limit')) {
            set_time_limit(300);
        }
        $source = self::manager()->decodePath($fs->path($path));
        $width = $source->width();
        $height = $source->height();

        self::deleteVariants($path, $disk);
        $widths = self::targetWidths($width);
        foreach ($widths as $w) {
            $resized = (clone $source)->scaleDown(width: $w);
            foreach (self::formats() as $format) {
                $file = self::variantPath($path, $w, $format);
                $fs->makeDirectory(dirname($file));
                $resized->encode(match ($format) {
                    'avif' => new AvifEncoder(quality: self::QUALITY['avif'], strip: true),
                    'webp' => new WebpEncoder(quality: self::QUALITY['webp'], strip: true),
                    'jpg' => new JpegEncoder(quality: self::QUALITY['jpg'], progressive: true, strip: true),
                })->save($fs->path($file));
            }
        }

        return ['width' => $width, 'height' => $height, 'widths' => $widths, 'formats' => self::formats()];
    }

    /** Formats that can be written here, best first. */
    public static function formats(): array
    {
        if (self::$formatsOverride !== null) {
            return self::$formatsOverride;
        }
        if (self::imagick()) {
            $can = fn ($f) => (bool) \Imagick::queryFormats(strtoupper($f));
        } else {
            $gd = function_exists('gd_info') ? gd_info() : [];
            $can = fn ($f) => match ($f) {
                'avif' => function_exists('imageavif') && ! empty($gd['AVIF Support']),
                'webp' => function_exists('imagewebp') && ! empty($gd['WebP Support']),
            };
        }

        return [...array_filter(['avif', 'webp'], $can), 'jpg'];
    }

    /** Meta for an image whose variants were made earlier (by looking at the disk). */
    public static function inspect(string $path, string $disk = 'public'): ?array
    {
        $fs = Storage::disk($disk);
        if (! $fs->exists($path) || ! ($size = @getimagesize($fs->path($path)))) {
            return null;
        }
        $widths = array_values(array_filter(
            self::targetWidths($size[0]),
            fn ($w) => $fs->exists(self::variantPath($path, $w, 'jpg')),
        ));

        $first = $widths[0] ?? null;
        $formats = $first ? array_values(array_filter(self::FORMATS, fn ($f) => $fs->exists(self::variantPath($path, $first, $f)))) : [];

        return ['width' => $size[0], 'height' => $size[1], 'widths' => $widths, 'formats' => $formats];
    }

    public static function deleteVariants(?string $path, string $disk = 'public'): void
    {
        if (! $path) {
            return;
        }
        $fs = Storage::disk($disk);
        $fs->delete(collect(self::WIDTHS)
            ->crossJoin(self::FORMATS)
            ->map(fn ($p) => self::variantPath($path, $p[0], $p[1]))
            ->all());
    }

    /** Delete an uploaded image and its variants. */
    public static function delete(?string $path, string $disk = 'public'): void
    {
        if (! $path) {
            return;
        }
        self::deleteVariants($path, $disk);
        Storage::disk($disk)->delete($path);
    }

    public static function variantPath(string $path, int $width, string $format): string
    {
        $info = pathinfo($path);
        $dir = ($info['dirname'] ?? '.') === '.' ? '' : $info['dirname'].'/';

        return "variants/{$dir}{$info['filename']}-{$width}.{$format}";
    }

    /** @return list<int> */
    public static function targetWidths(int $original): array
    {
        $max = max(self::WIDTHS);
        $widths = array_values(array_filter(self::WIDTHS, fn ($w) => $w < $original));
        $widths[] = min($original, $max);

        return array_values(array_unique($widths));
    }

    /** Imagick is used when it can write AVIF; otherwise GD. */
    private static function imagick(): bool
    {
        return extension_loaded('imagick') && \Imagick::queryFormats('AVIF');
    }

    private static function manager(): ImageManager
    {
        return ImageManager::usingDriver(self::imagick() ? ImagickDriver::class : GdDriver::class);
    }
}
