<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CollectionSnippet extends Pivot
{
    use HasUuids;

    protected $table = 'collection_snippet';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'collection_id',
        'snippet_id',
        'position',
        'note',
        'added_at',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'added_at' => 'datetime',
        ];
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function snippet(): BelongsTo
    {
        return $this->belongsTo(Snippet::class);
    }
}
