<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * توکن امن برای لینک «بررسی خبر».
 * در دیتابیس فقط هش SHA-256 توکن ذخیره می‌شود.
 */
class PostReviewToken extends Model
{
    protected $fillable = [
        'post_id',
        'token_hash',
        'expires_at',
        'consumed_at',
        'consumed_by_ip',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * آیا توکن معتبر است (منقضی یا مصرف نشده)؟
     */
    public function isValid(): bool
    {
        return $this->consumed_at === null && $this->expires_at->isFuture();
    }

    /**
     * مصرف یک‌بارِ توکن؛ اگر قبلا مصرف شده false برمی‌گرداند.
     */
    public function consume(string $ip): bool
    {
        return (bool) static::query()
            ->whereKey($this->getKey())
            ->whereNull('consumed_at')
            ->update([
                'consumed_at' => now(),
                'consumed_by_ip' => substr($ip, 0, 45),
            ]);
    }
}
