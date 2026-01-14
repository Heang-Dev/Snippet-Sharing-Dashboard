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

class Team extends Model
{
    use HasFactory, HasSlug, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'avatar_url',
        'owner_id',
        'privacy',
        'member_count',
        'snippet_count',
        'allow_member_invite',
        'default_snippet_privacy',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'member_count' => 'integer',
            'snippet_count' => 'integer',
            'allow_member_invite' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_members')
            ->using(TeamMember::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }

    public function snippets(): HasMany
    {
        return $this->hasMany(Snippet::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(Share::class);
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function getMemberRole(User $user): ?string
    {
        $member = $this->members()->where('user_id', $user->id)->first();
        return $member?->pivot?->role;
    }

    public function isOwner(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
