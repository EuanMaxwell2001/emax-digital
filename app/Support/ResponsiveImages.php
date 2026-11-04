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
 *   ['width' => 1600, 'height' => 1000, 'widths' => [480, 640, 960, 1280, 1400, 1600]]
 */
class ResponsiveImages
{
    /** Every width any <x-picture> asks for. */
    public const WIDTHS = [320, 480, 640, 960, 1280, 1400, 1600];

    public const FORMATS = ['avif', 'webp', 'jpg'];

    /** Encoder quality per format (close to Astro/sharp's defaults). */
    private const QUALITY = ['avif' => 55, 'webp' => 80, 'jpg' => 80];

    /** Create every variant of an image and return its meta. */
    public static function generate(string $path, string $disk = 'public'): ?array
    {
        $fs = Storage::disk($disk);
        if (! $fs->exists($path)) {
            return null;
        }

        @set_time_limit(300);
        $source = self::manager()->decodePath($fs->path($path));
        $width = $source->width();
        $height = $source->height();

        self::deleteVariants($path, $disk);
        $widths = self::targetWidths($width);
        foreach ($widths as $w) {
            $resized = (clone $source)->scaleDown(width: $w);
            foreach (self::FORMATS as $format) {
                $file = self::variantPath($path, $w, $format);
                $fs->makeDirectory(dirname($file));
                $resized->encode(match ($format) {
                    'avif' => new AvifEncoder(quality: self::QUALITY['avif'], strip: true),
                    'webp' => new WebpEncoder(quality: self::QUALITY['webp'], strip: true),
                    'jpg' => new JpegEncoder(quality: self::QUALITY['jpg'], progressive: true, strip: true),
                })->save($fs->path($file));
            }
        }

        return ['width' => $width, 'height' => $height, 'widths' => $widths];
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

        return ['width' => $size[0], 'height' => $size[1], 'widths' => $widths];
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

    private static function manager(): ImageManager
    {
        $imagick = extension_loaded('imagick') && \Imagick::queryFormats('AVIF');

        return ImageManager::usingDriver($imagick ? ImagickDriver::class : GdDriver::class);
    }
}
