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
        'privacy',
        'snippet_count',
    ];

    protected function casts(): array
    {
        return [
            'snippet_count' => 'integer',
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
            ->using(CollectionSnippet::class)
            ->withPivot('position', 'note')
            ->orderBy('collection_snippet.position');
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isPublic(): bool
    {
        return $this->privacy === 'public';
    }

    public function isPrivate(): bool
    {
        return $this->privacy === 'private';
    }

    public function scopePublic($query)
    {
        return $query->where('privacy', 'public');
    }

    public function scopeVisible($query, ?User $user = null)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('privacy', 'public');

            if ($user) {
                $q->orWhere('user_id', $user->id);
            }
        });
    }
}
