<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SourceChannel extends Model
{
    protected $fillable = [
        'name',
        'platform',
        'channel_link',
        'is_active',
        'fetch_interval_minutes',
        'last_fetched_at',
        'filter_keywords',
        'remove_links',
        'remove_tags',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'remove_links' => 'boolean',
        'remove_tags' => 'boolean',
        'last_fetched_at' => 'datetime',
    ];

    public function aggregatedMessages(): HasMany
    {
        return $this->hasMany(AggregatedMessage::class);
    }
}
