<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AggregatedMessage extends Model
{
    protected $fillable = [
        'source_channel_id',
        'platform',
        'external_message_id',
        'raw_content',
        'cleaned_content',
        'media_urls',
        'local_media_paths',
        'status',
        'status_reason',
        'post_id',
        'error_message',
    ];

    protected $casts = [
        'media_urls' => 'array',
        'local_media_paths' => 'array',
    ];

    public function sourceChannel(): BelongsTo
    {
        return $this->belongsTo(SourceChannel::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
