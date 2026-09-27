<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\BaleLinkCode;
use App\Services\BaleBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

/**
 * مدیریت اتصال بله توسط خود کاربر (نویسنده یا ادمین).
 * کاربر در پنل کد می‌گیرد، در ربات ارسال می‌کند و اتصال خودکار انجام می‌شود.
 */
class BaleLinkController extends Controller
{
    /**
     * صفحه «اتصال بله» برای کاربر لاگین‌شده (نویسنده یا ادمین).
     * لایه نمایش بر اساس نقش انتخاب می‌شود تا در پنل خود کاربر باز شود.
     */
    public function index()
    {
        $user = Auth::guard('admin')->user();
        $isAuthor = $user->hasRole('author') && ! ($user->hasRole('admin') || $user->hasRole('super-admin'));

        // آخرین کد فعال (اگر هست دوباره همان را نشان بده تا کد اضافه نسازد)
        $activeCode = BaleLinkCode::where('admin_id', $user->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        $bot = new BaleBotService();

        return view('account.bale-link', [
            'user' => $user,
            'isAuthor' => $isAuthor,
            'activeCode' => $activeCode,
            'chatId' => $user->bale_chat_id,
            'botConfigured' => $bot->isConfigured(),
            'layout' => $isAuthor ? 'author.layout.master' : 'admin.layout.master',
            'section' => $isAuthor ? 'author-content' : 'admin-content',
        ]);
    }

    /**
     * ساخت کد اتصال جدید.
     */
    public function createCode(Request $request)
    {
        $user = Auth::guard('admin')->user();

        // حداکثر ۵ کد در ۱۰ دقیقه برای هر کاربر
        $key = 'bale-code:' . $user->id;

        if (! RateLimiter::attempt($key, 5, fn () => true, 600)) {
            return back()->with('error', 'تعداد کدهای ساخته‌شده زیاد است؛ چند دقیقه دیگر تلاش کنید.');
        }

        // کدهای قبلیِ استفاده‌نشده را باطل کن
        BaleLinkCode::where('admin_id', $user->id)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        $code = BaleLinkCode::generateFor($user);

        return back()->with('new_code', $code->code);
    }

    /**
     * قطع اتصال بله.
     */
    public function unlink(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $user->update(['bale_chat_id' => null]);

        return back()->with('success', 'اتصال بله قطع شد. می‌توانید دوباره با کد جدید وصل شوید.');
    }
}
