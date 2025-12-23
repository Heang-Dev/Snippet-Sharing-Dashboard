<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Language extends Model
{
    use HasFactory, HasSlug, HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'display_name',
        'pygments_lexer',
        'monaco_language',
        'file_extensions',
        'icon',
        'color',
        'snippet_count',
        'popularity_rank',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'file_extensions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function snippets(): HasMany
    {
        return $this->hasMany(Snippet::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
