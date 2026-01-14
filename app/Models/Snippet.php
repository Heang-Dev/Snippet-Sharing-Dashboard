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
        'team_id',
        'category_id',
        'title',
        'description',
        'code',
        'highlighted_html',
        'language',
        'privacy',
        'slug',
        'version_number',
        'parent_snippet_id',
        'is_fork',
        'is_featured',
        'allow_comments',
        'allow_forks',
        'license',
        'view_count',
        'unique_view_count',
        'fork_count',
        'favorite_count',
        'comment_count',
        'share_count',
        'trending_score',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'version_number' => 'integer',
            'view_count' => 'integer',
            'unique_view_count' => 'integer',
            'fork_count' => 'integer',
            'favorite_count' => 'integer',
            'comment_count' => 'integer',
            'share_count' => 'integer',
            'trending_score' => 'float',
            'is_fork' => 'boolean',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'allow_forks' => 'boolean',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function parentSnippet(): BelongsTo
    {
        return $this->belongsTo(Snippet::class, 'parent_snippet_id');
    }

    public function forks(): HasMany
    {
        return $this->hasMany(Snippet::class, 'parent_snippet_id');
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
            ->using(Favorite::class)
            ->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_comment_id');
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_snippet')
            ->using(CollectionSnippet::class)
            ->withPivot('position', 'note');
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
        return $query->where('privacy', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('privacy', 'private');
    }

    public function scopeTeamVisible($query)
    {
        return $query->where('privacy', 'team');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeTrending($query)
    {
        return $query->orderByDesc('trending_score');
    }

    public function scopeVisible($query, ?User $user = null)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('privacy', 'public');

            if ($user) {
                $q->orWhere('user_id', $user->id);

                $teamIds = $user->teams()->pluck('teams.id');
                if ($teamIds->isNotEmpty()) {
                    $q->orWhere(function ($teamQuery) use ($teamIds) {
                        $teamQuery->where('privacy', 'team')
                            ->whereIn('team_id', $teamIds);
                    });
                }
            }
        });
    }

    public function isPublic(): bool
    {
        return $this->privacy === 'public';
    }

    public function isPrivate(): bool
    {
        return $this->privacy === 'private';
    }

    public function isTeamVisible(): bool
    {
        return $this->privacy === 'team';
    }

    public function isUnlisted(): bool
    {
        return $this->privacy === 'unlisted';
    }

    public function isFork(): bool
    {
        return $this->is_fork;
    }

    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function canBeViewedBy(?User $user): bool
    {
        if ($this->isPublic() || $this->isUnlisted()) {
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
        $this->increment('view_count');
    }

    public function incrementUniqueViewCount(): void
    {
        $this->increment('unique_view_count');
    }
}
