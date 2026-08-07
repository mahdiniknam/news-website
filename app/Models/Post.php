<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'author_id',
        'category_id',
        'approved_by',
        'title',
        'slug',
        'content',
        'featured_image',
        'status',
        'rejection_reason',
        'approved_at',
        'published_at',
        'view_count',
        'is_featured',
        'meta_title',
        'meta_description',
        'type',
    ];

    protected $casts = [
        'is_featured' => 'boolean',

        'approved_at' => 'datetime',
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];

    protected $dates = [
        'approved_at',
        'published_at',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = $post->generateSlug($post->title);
            }

            // تنظیم خودکار وضعیت بر اساس نقش کاربر
            $user = Auth::guard('admin')->user();
            if ($user && $user->hasRole('super-admin') || $user->hasRole('admin')) {
                $post->status = 'published';
                $post->published_at = now();
            } else {
                $post->status = 'pending';
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = $post->generateSlug($post->title);
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
            $slug = 'post-' . time();
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

    // روابط
    public function author()
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    // اسکوپ‌ها
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // متدهای کمکی
    public static function statuses()
    {
        return [
            'draft' => 'پیش‌نویس',
            'pending' => 'در انتظار بررسی',
            'approved' => 'تأیید شده',
            'rejected' => 'رد شده',
            'published' => 'منتشر شده',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function getStatusBadgeAttribute()
    {
        $colors = [
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'info',
            'rejected' => 'danger',
            'published' => 'success',
        ];

        $color = $colors[$this->status] ?? 'secondary';
        return "<span class='badge bg-{$color}'>{$this->status_label}</span>";
    }


    public static function types()
    {
        return [
            'news' => 'خبر',
            'note' => 'یادداشت',
            'interview' => 'مصاحبه',
        ];
    }

    public function getTypeLabelAttribute()
    {
        return self::types()[$this->type] ?? $this->type;
    }

    public function getTypeBadgeAttribute()
    {
        $colors = [
            'new' => 'secondary',
            'note' => 'warning',
            'interview' => 'info',  
        ];

        $color = $colors[$this->type] ?? 'secondary';
        return "<span class='badge bg-{$color}'>{$this->type_label}</span>";
    }
















    public function getExcerptAttribute($length = 150)
    {
        return Str::limit(strip_tags($this->content), $length);
    }

    public function getReadingTimeAttribute()
    {
        $words = str_word_count(strip_tags($this->content));
        $minutes = ceil($words / 200);
        return $minutes . ' دقیقه';
    }

    // متد برای انتشار
    public function publish()
    {
        $this->status = 'published';
        $this->published_at = now();
        $this->save();
    }

    // متد برای تایید
    public function approve($adminId)
    {
        $this->status = 'approved';
        $this->approved_by = $adminId;
        $this->approved_at = now();
        $this->save();
    }

    // متد برای رد
    public function reject($reason)
    {
        $this->status = 'rejected';
        $this->rejection_reason = $reason;
        $this->save();
    }

    // متد برای افزایش بازدید
    public function incrementViews()
    {
        $this->increment('view_count');
    }

    // متد برای بررسی اینکه آیا کاربر می‌تواند خبر را ویرایش کند
    public function canEdit()
    {
        $user = Auth::guard('admin')->user();

        // سوپرادمین و ادمین می‌توانند همه را ویرایش کنند
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            return true;
        }

        // نویسنده فقط می‌تواند خبرهای خود را ویرایش کند
        return $this->author_id == $user->id && in_array($this->status, ['draft', 'pending', 'rejected']);
    }

    // متد برای بررسی اینکه آیا کاربر می‌تواند خبر را حذف کند
    public function canDelete()
    {
        $user = Auth::guard('admin')->user();

        // سوپرادمین و ادمین می‌توانند همه را حذف کنند
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            return true;
        }

        // نویسنده فقط می‌تواند خبرهای خود را حذف کند
        return $this->author_id == $user->id && in_array($this->status, ['draft', 'pending', 'rejected']);
    }
}
