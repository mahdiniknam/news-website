<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    protected $fillable = [
        'name',

        'status',
    ];

    const STATUS_ACTIE = 'active';
    const STATUS_IN_ACTIE = 'in_active';

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIE => 'فعال',
            self::STATUS_IN_ACTIE => 'غیرفعال',

        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statuses()[$this->status] ?? 'نامشخص';
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            $tag->slug = $tag->generateSlug($tag->name);
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name')) {
                $tag->slug = $tag->generateSlug($tag->name);
            }
        });
    }

    public function generateSlug($text)
    {
        // اگر متن فارسی بود
        if ($this->isPersian($text)) {
            $slug = $this->persianSlug($text);
        } else {
            $slug = Str::slug($text);
        }

        // خالی بودن اسلاگ
        if (empty($slug)) {
            $slug = 'tag-' . time();
        }

        // یکتا کردن اسلاگ
        return $this->makeUniqueSlug($slug);
    }

    private function isPersian($text)
    {
        return preg_match('/[\x{0600}-\x{06FF}]/u', $text);
    }

    private function persianSlug($text)
    {
        // تبدیل حروف فارسی
        $search = [' ', '،', '؟', '!', ';', ':', '(', ')', '[', ']', '{', '}'];
        $replace = ['-', '', '', '', '', '', '', '', '', '', '', ''];
        $text = str_replace($search, $replace, $text);

        // حذف کاراکترهای خاص
        $text = preg_replace('/[^\x{0600}-\x{06FF}\x{2000}-\x{206F}\x{0020}-\x{007E}]+/u', '', $text);

        return trim($text, '-');
    }

    private function makeUniqueSlug($slug)
    {
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

   
}
