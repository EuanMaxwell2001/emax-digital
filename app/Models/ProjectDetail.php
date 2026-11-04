<?php

namespace App\Models;

use App\Models\Concerns\HasResponsiveImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A close-up image on a case study ("Up close"), with its caption and layout shape. */
class ProjectDetail extends Model
{
    use HasResponsiveImages;

    /** Layout shapes, matching the .detail--{shape} styles. */
    public const SHAPES = [
        'wide' => 'Wide (large, full width)',
        'strip' => 'Strip (long and thin)',
        'square' => 'Square-ish',
        'small' => 'Small',
    ];

    protected array $responsiveImages = ['image_path' => 'image_meta'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['image_meta' => 'array'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
