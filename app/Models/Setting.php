<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * تنظیمات سایت (key/value).
 * مقادیر حساس (مثل توکن ربات) با Crypt رمزنگاری و فقط در همین مدل باز می‌شوند.
 */
class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'is_encrypted'];

    protected $casts = ['is_encrypted' => 'boolean'];

    public static function get(string $key, ?string $default = null): ?string
    {
        $row = static::find($key);

        if (! $row || $row->value === null) {
            return $default;
        }

        try {
            return $row->is_encrypted ? Crypt::decryptString($row->value) : $row->value;
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function set(string $key, ?string $value, bool $encrypt = false): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value === null ? null : ($encrypt ? Crypt::encryptString($value) : $value),
                'is_encrypted' => $encrypt,
            ]
        );
    }
}
