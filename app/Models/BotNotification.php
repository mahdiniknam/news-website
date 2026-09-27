<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * آرشیو پیام‌های ارسالی ربات بله.
 */
class BotNotification extends Model
{
    protected $fillable = [
        'chat_id',
        'subject',
        'notifiable_type',
        'notifiable_id',
        'payload',
        'status',
        'error_message',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
