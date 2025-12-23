<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Collection extends Model
{
    use HasFactory, HasSlug, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'visibility',
        'snippets_count',
    ];

    protected function casts(): array
    {
        return [
            'snippets_count' => 'integer',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function snippets(): BelongsToMany
    {
        return $this->belongsToMany(Snippet::class, 'collection_snippet')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderBy('collection_snippet.sort_order');
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    public function isPrivate(): bool
    {
        return $this->visibility === 'private';
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeVisible($query, ?User $user = null)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('visibility', 'public');

            if ($user) {
                $q->orWhere('user_id', $user->id);
            }
        });
    }
}
