<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SnippetVersion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'snippet_id',
        'version_number',
        'title',
        'description',
        'code',
        'language',
        'change_summary',
        'change_type',
        'lines_added',
        'lines_removed',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'version_number' => 'integer',
            'lines_added' => 'integer',
            'lines_removed' => 'integer',
        ];
    }

    public function snippet(): BelongsTo
    {
        return $this->belongsTo(Snippet::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isInitialVersion(): bool
    {
        return $this->version_number === 1 || $this->change_type === 'create';
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('version_number', 'desc');
    }
}
