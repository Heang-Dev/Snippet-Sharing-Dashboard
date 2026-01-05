<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TeamMember extends Pivot
{
    use HasUuids;

    protected $table = 'team_members';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'can_create_snippets',
        'can_edit_snippets',
        'can_delete_snippets',
        'can_manage_members',
        'can_invite_members',
        'invited_by',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'can_create_snippets' => 'boolean',
            'can_edit_snippets' => 'boolean',
            'can_delete_snippets' => 'boolean',
            'can_manage_members' => 'boolean',
            'can_invite_members' => 'boolean',
            'joined_at' => 'datetime',
        ];
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
