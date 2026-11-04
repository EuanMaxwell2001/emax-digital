<?php

namespace App\Models\Concerns;

use App\Jobs\GenerateImageVariants;
use App\Support\ResponsiveImages;

/**
 * For models with uploaded images. Declare the image attributes as path => meta column:
 *   protected array $responsiveImages = ['cover_path' => 'cover_meta'];
 * When a path changes, the old file and its sizes are deleted and new sizes are generated
 * after the response. Deleting the model deletes its files.
 */
trait HasResponsiveImages
{
    public static function bootHasResponsiveImages(): void
    {
        static::saving(function (self $model) {
            foreach ($model->responsiveImages as $path => $meta) {
                // A new image invalidates the old sizes (unless the meta is being set with it).
                if ($model->isDirty($path) && ! $model->isDirty($meta)) {
                    $model->{$meta} = null;
                }
            }
        });

        static::saved(function (self $model) {
            foreach ($model->responsiveImages as $path => $meta) {
                if (! $model->wasChanged($path) && ! $model->wasRecentlyCreated) {
                    continue;
                }
                $old = $model->getOriginal($path);
                if ($model->wasChanged($path) && $old && $old !== $model->{$path}) {
                    ResponsiveImages::delete($old);
                }
                if ($model->{$path} && ! $model->{$meta}) {
                    GenerateImageVariants::dispatchAfterResponse($model::class, $model->getKey(), $path, $meta);
                }
            }
        });

        static::deleted(function (self $model) {
            foreach (array_keys($model->responsiveImages) as $path) {
                ResponsiveImages::delete($model->{$path});
            }
        });
    }
}
