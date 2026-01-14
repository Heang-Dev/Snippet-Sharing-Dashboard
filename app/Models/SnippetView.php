<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SnippetView extends Model
{
    use HasFactory, HasUuids;

    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'snippet_id',
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'referrer',
        'country',
        'city',
        'viewed_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
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

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBySnippet($query, $snippetId)
    {
        return $query->where('snippet_id', $snippetId);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('viewed_at', '>=', now()->subDays($days));
    }
}
