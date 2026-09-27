<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * کد اتصال یک‌بارمصرف برای پیوند دادن اکانت بله کاربر.
 */
class BaleLinkCode extends Model
{
    protected $fillable = [
        'admin_id',
        'code',
        'expires_at',
        'used_at',
        'linked_chat_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function isValid(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }

    /**
     * تولید کد ۸ رقمی غیرتکراری.
     */
    public static function generateFor(Admin $admin, int $minutes = 15): self
    {
        do {
            $code = (string) random_int(10000000, 99999999);
        } while (static::where('code', $code)->where('expires_at', '>', now())->exists());

        return static::create([
            'admin_id' => $admin->id,
            'code' => $code,
            'expires_at' => now()->addMinutes($minutes),
        ]);
    }
}
