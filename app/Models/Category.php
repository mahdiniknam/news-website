<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Boot method برای تولید خودکار اسلاگ
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = $category->generateSlug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = $category->generateSlug($category->name);
            }
        });
    }

    // تولید اسلاگ
    public function generateSlug($text)
    {
        if ($this->isPersian($text)) {
            $slug = $this->persianSlug($text);
        } else {
            $slug = Str::slug($text);
        }

        if (empty($slug)) {
            $slug = 'category-' . time();
        }

        return $this->makeUniqueSlug($slug);
    }

    private function isPersian($text)
    {
        return preg_match('/[\x{0600}-\x{06FF}]/u', $text);
    }

    private function persianSlug($text)
    {
        $search = [' ', '،', '؟', '!', ';', ':', '(', ')', '[', ']', '{', '}'];
        $replace = ['-', '', '', '', '', '', '', '', '', '', '', ''];
        $text = str_replace($search, $replace, $text);
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

    // رابطه با والد
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // رابطه با زیردسته‌ها
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // رابطه بازگشتی برای همه زیردسته‌ها
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    // اسکوپ فعال
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // اسکوپ مرتب‌سازی
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }

    // متد کمکی برای وضعیت
    public static function statuses()
    {
        return [
            1 => 'فعال',
            0 => 'غیرفعال',
        ];
    }

    // متد کمکی برای وضعیت
    public function getStatusLabelAttribute()
    {
        return $this->status ? 'فعال' : 'غیرفعال';
    }

    // متد کمکی برای نمایش دسته‌بندی
    public function getFullNameAttribute()
    {
        if ($this->parent) {
            return $this->parent->full_name . ' -> ' . $this->name;
        }
        return $this->name;
    }
}
