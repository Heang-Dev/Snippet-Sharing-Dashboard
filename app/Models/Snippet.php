<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Snippet extends Model
{
    use HasFactory, HasSlug, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'language_id',
        'category_id',
        'team_id',
        'title',
        'slug',
        'description',
        'code',
        'highlighted_code',
        'file_name',
        'visibility',
        'password_hash',
        'expires_at',
        'views_count',
        'favorites_count',
        'comments_count',
        'forks_count',
        'forked_from_id',
        'version',
        'is_pinned',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'views_count' => 'integer',
            'favorites_count' => 'integer',
            'comments_count' => 'integer',
            'forks_count' => 'integer',
            'version' => 'integer',
            'is_pinned' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function forkedFrom(): BelongsTo
    {
        return $this->belongsTo(Snippet::class, 'forked_from_id');
    }

    public function forks(): HasMany
    {
        return $this->hasMany(Snippet::class, 'forked_from_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(SnippetVersion::class)->orderByDesc('version_number');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'snippet_tag')
            ->withTimestamps();
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')
            ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_snippet')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function views(): HasMany
    {
        return $this->hasMany(SnippetView::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(Share::class);
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('visibility', 'private');
    }

    public function scopeTeamVisible($query)
    {
        return $query->where('visibility', 'team');
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeVisible($query, ?User $user = null)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('visibility', 'public');

            if ($user) {
                $q->orWhere('user_id', $user->id);

                $teamIds = $user->teams()->pluck('teams.id');
                if ($teamIds->isNotEmpty()) {
                    $q->orWhere(function ($teamQuery) use ($teamIds) {
                        $teamQuery->where('visibility', 'team')
                            ->whereIn('team_id', $teamIds);
                    });
                }
            }
        });
    }

    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    public function isPrivate(): bool
    {
        return $this->visibility === 'private';
    }

    public function isTeamVisible(): bool
    {
        return $this->visibility === 'team';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function canBeViewedBy(?User $user): bool
    {
        if ($this->isExpired()) {
            return false;
        }

        if ($this->isPublic()) {
            return true;
        }

        if (!$user) {
            return false;
        }

        if ($this->isOwnedBy($user)) {
            return true;
        }

        if ($this->isTeamVisible() && $this->team_id) {
            return $this->team->hasMember($user);
        }

        return false;
    }

    public function incrementViewCount(): void
    {
        $this->increment('views_count');
    }
}
