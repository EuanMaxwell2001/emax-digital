<?php

namespace App\Models;

use App\Models\Concerns\HasResponsiveImages;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasResponsiveImages;

    protected array $responsiveImages = ['cover_path' => 'cover_meta'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'services' => 'array',
            'cover_meta' => 'array',
            'is_published' => 'boolean',
            'year' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function details(): HasMany
    {
        return $this->hasMany(ProjectDetail::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /** The next published project in order, wrapping round to the first. */
    public function next(): ?self
    {
        $all = static::published()->ordered()->get(['id', 'slug', 'title', 'sector']);
        $i = $all->search(fn ($p) => $p->id === $this->id);

        return $i === false ? $all->first() : $all[($i + 1) % $all->count()];
    }

    public function url(): string
    {
        return route('work.show', $this);
    }
}
