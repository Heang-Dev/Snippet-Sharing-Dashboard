<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'snippet_id',
        'user_id',
        'parent_comment_id',
        'content',
        'line_number',
        'is_edited',
        'edited_at',
        'upvote_count',
        'reply_count',
        'is_pinned',
        'is_resolved',
    ];

    protected function casts(): array
    {
        return [
            'line_number' => 'integer',
            'is_edited' => 'boolean',
            'edited_at' => 'datetime',
            'upvote_count' => 'integer',
            'reply_count' => 'integer',
            'is_pinned' => 'boolean',
            'is_resolved' => 'boolean',
        ];
    }

    public function snippet(): BelongsTo
    {
        return $this->belongsTo(Snippet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_comment_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_comment_id');
    }

    public function isReply(): bool
    {
        return !is_null($this->parent_comment_id);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isPinned(): bool
    {
        return $this->is_pinned;
    }

    public function isResolved(): bool
    {
        return $this->is_resolved;
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_comment_id');
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }
}
