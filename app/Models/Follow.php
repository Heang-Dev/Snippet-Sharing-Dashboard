<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Follow extends Pivot
{
    use HasUuids;

    protected $table = 'follows';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'follower_id',
        'following_id',
        'notification_enabled',
    ];

    protected function casts(): array
    {
        return [
            'notification_enabled' => 'boolean',
        ];
    }

    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function following(): BelongsTo
    {
        return $this->belongsTo(User::class, 'following_id');
    }
}
