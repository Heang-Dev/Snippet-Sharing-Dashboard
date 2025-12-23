<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Tag extends Model
{
    use HasFactory, HasSlug, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'usage_count',
    ];

    protected function casts(): array
    {
        return [
            'usage_count' => 'integer',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function snippets(): BelongsToMany
    {
        return $this->belongsToMany(Snippet::class, 'snippet_tag')
            ->withTimestamps();
    }

    public function incrementUsageCount(): void
    {
        $this->increment('usage_count');
    }

    public function decrementUsageCount(): void
    {
        $this->decrement('usage_count');
    }

    public function scopePopular($query, int $limit = 20)
    {
        return $query->orderByDesc('usage_count')->limit($limit);
    }
}
