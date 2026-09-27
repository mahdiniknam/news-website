<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\BotNotification;
use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * ساختن و ارسال اعلان‌های گردش کار خبر در ربات بله.
 *
 * - وقتی خبرنگار خبر ثبت می‌کند → پیام + لینک بررسی برای همه مدیران (admin/super-admin)
 * - وقتی مدیر تایید می‌کند → پیام برای خبرنگار
 * - وقتی مدیر با دلیل رد می‌کند → پیام + دلیل برای خبرنگار
 * - وقتی خبر منتشر می‌شود → پیام برای خبرنگار
 */
class PostNotifier
{
    public function __construct(protected BaleBotService $bot) {}

    /**
     * اعلان «خبر جدید در انتظار تایید» برای همه مدیران.
     */
    public function notifyAdminsNewPost(Post $post): void
    {
        $recipients = $this->adminRecipients();

        if ($recipients->isEmpty()) {
            return;
        }

        $link = $this->createReviewLink($post);

        $title = e($post->title);
        $author = e($post->author?->name ?? 'نامشخص');
        $category = e($post->category?->name ?? 'بدون دسته‌بندی');
        $type = e($post->type_label);
        $excerpt = e(Str::limit(strip_tags($post->content), 200));

        $text = <<<HTML
        📝 <b>خبر جدید در انتظار تایید</b>

        <b>عنوان:</b> {$title}
        <b>نویسنده:</b> {$author}
        <b>دسته‌بندی:</b> {$category}
        <b>نوع:</b> {$type}

        {$excerpt}

        برای مشاهده و تایید یا رد خبر، روی دکمه زیر بزنید:
        <a href="{$link}">🔎 بررسی و تصمیم‌گیری</a>
        HTML;

        foreach ($recipients as $admin) {
            $this->dispatch($admin->bale_chat_id, $text, 'new_post_review', $admin, $post);
        }
    }

    /**
     * اعلان «ارسال مجدد خبر رد‌شده» برای همه مدیران.
     * نویسنده خبر رد‌شده را ویرایش و دوباره برای بررسی ارسال کرده است.
     */
    public function notifyAdminsResubmittedPost(Post $post, ?string $previousReason = null): void
    {
        $recipients = $this->adminRecipients();

        if ($recipients->isEmpty()) {
            return;
        }

        $link = $this->createReviewLink($post);

        $title = e($post->title);
        $author = e($post->author?->name ?? 'نامشخص');
        $previousReason = e($previousReason ? Str::limit($previousReason, 150) : '-');

        $text = <<<HTML
        🔄 <b>خبر رد‌شده مجدداً ارسال شد</b>

        <b>عنوان:</b> {$title}
        <b>نویسنده:</b> {$author}
        <b>دلیل رد قبلی:</b> {$previousReason}

        نویسنده پس از اصلاح، خبر را دوباره برای بررسی ارسال کرده است.
        برای مشاهده و تایید یا رد، روی دکمه زیر بزنید:
        <a href="{$link}">🔎 بررسی و تصمیم‌گیری</a>
        HTML;

        foreach ($recipients as $admin) {
            $this->dispatch($admin->bale_chat_id, $text, 'resubmitted_post_review', $admin, $post);
        }
    }

    /**
     * اعلان «تایید خبر» برای نویسنده.
     */
    public function notifyAuthorApproved(Post $post): void
    {
        $author = $post->author;
        if (! $author || ! $author->bale_chat_id) {
            return;
        }

        $title = e($post->title);
        $approver = e($post->approver?->name ?? 'مدیر سایت');

        $text = <<<HTML
        ✅ <b>خبر شما تایید شد</b>

        <b>عنوان:</b> {$title}
        <b>تاییدکننده:</b> {$approver}

        خبر شما پس از انتشار روی سایت نمایش داده می‌شود.
        HTML;

        $this->dispatch($author->bale_chat_id, $text, 'post_approved', $author, $post);
    }

    /**
     * اعلان «رد خبر» همراه دلیل برای نویسنده.
     */
    public function notifyAuthorRejected(Post $post): void
    {
        $author = $post->author;
        if (! $author || ! $author->bale_chat_id) {
            return;
        }

        $title = e($post->title);
        $reason = e($post->rejection_reason ?: 'دلیلی ثبت نشده است');

        $text = <<<HTML
        ❌ <b>خبر شما رد شد</b>

        <b>عنوان:</b> {$title}

        <b>دلیل رد:</b>
        {$reason}

        پس از اصلاح می‌توانید دوباره خبر را برای بررسی ارسال کنید.
        HTML;

        $this->dispatch($author->bale_chat_id, $text, 'post_rejected', $author, $post);
    }

    /**
     * اعلان «انتشار خبر» برای نویسنده.
     */
    public function notifyAuthorPublished(Post $post): void
    {
        $author = $post->author;
        if (! $author || ! $author->bale_chat_id) {
            return;
        }

        $title = e($post->title);
        $url = route('post.show', ['slug' => $post->slug]);

        $text = <<<HTML
        📰 <b>خبر شما منتشر شد</b>

        <b>عنوان:</b> {$title}

        مشاهده روی سایت:
        <a href="{$url}">{$url}</a>
        HTML;

        $this->dispatch($author->bale_chat_id, $text, 'post_published', $author, $post);
    }

    /**
     * ساخت توکن امن (هش‌شده در دیتابیس) برای لینک بررسی.
     */
    protected function createReviewLink(Post $post): string
    {
        $token = Str::random(48);

        $post->reviewTokens()->create([
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addDays(3),
        ]);

        return route('posts.review.show', ['token' => $token]);
    }

    /**
     * مدیران دارای بله که باید «خبر جدید» را ببینند.
     */
    protected function adminRecipients()
    {
        return Admin::where('is_active', true)
            ->whereNotNull('bale_chat_id')
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['admin', 'super-admin']);
            })
            ->get();
    }

    /**
     * ارسال به صف (queue) و ثبت در آرشیو.
     */
    protected function dispatch(?string $chatId, string $text, string $subject, Admin $notifiable, Post $post): void
    {
        if (empty($chatId)) {
            return;
        }

        SendBaleMessage::dispatch(
            (string) $chatId,
            $text,
            $subject,
            get_class($notifiable),
            $notifiable->id,
            $post->id
        );
    }
}
