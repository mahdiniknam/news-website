<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\BaleLinkCode;
use App\Models\Setting;
use App\Services\BaleBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Webhook ربات بله: پیام کاربر را می‌گیرد و اگر کد ۸ رقمی معتبر بود،
 * شناسه گفتگوی او را به اکانتش متصل می‌کند و پیام خوش‌آمد می‌فرستد.
 *
 * رفتار ربات از دید کاربر:
 *  1) /start  ← پیام خوش‌آمد + دستورالعمل گرفتن کد از پنل سایت
 *  2) هر متن دیگری ← همان راهنما (بدون پیام خطای خام)
 *  3) کد ۸ رقمی معتبر ← اتصال حساب + پیام موفقیت
 *  4) کد نامعتبر/منقضی ← پیام خطای شفاف با راهنمای اقدام بعدی
 *
 * امنیت:
 * - فقط درخواست‌های POST با secret_path (توکن تصادفی در URL) پذیرفته می‌شوند.
 * - کد یک‌بارمصرف است و ۱۵ دقیقه اعتبار دارد.
 * - rate-limit روی مسیر (throttle:120,1) اعمال می‌شود.
 */
class BaleWebhookController extends Controller
{
    public function __invoke(Request $request, string $secret, BaleBotService $bot)
    {
        // اعتبارسنجی secret مسیر (مقایسه زمان‌ثابت)
        $expected = (string) config('services.bale.webhook_secret');

        if ($expected === '' || ! hash_equals($expected, $secret)) {
            abort(404);
        }

        if (! $request->isJson()) {
            return response('bad request', 400);
        }

        $update = $request->json()->all();

        $message = $update['message'] ?? null;
        $text = trim((string) ($message['text'] ?? ''));
        $chatId = $message['chat']['id'] ?? null;

        if (! $message || $text === '' || $chatId === null) {
            return response('ok');
        }

        // ۱) دستور شروع: خوش‌آمدگویی + راهنمای گام‌به‌گام
        if ($this->isStartCommand($text)) {
            $bot->sendMessage($chatId, $this->welcomeMessage());

            return response('ok');
        }

        // ۲) کاربر قبلاً متصل است؟ فقط اطلاع بدهیم که نیازی به کد جدید ندارد
        $existing = Admin::where('bale_chat_id', (string) $chatId)->first();

        if ($existing) {
            $bot->sendMessage(
                $chatId,
                "👋 سلام " . e($existing->name ?? '') . "!\n\n"
                . "حساب شما قبلاً متصل شده است ✅\n"
                . "اعلان‌های تایید یا رد خبرها به همین گفتگو ارسال می‌شود.\n\n"
                . "اگر حساب دیگری می‌خواهید متصل کنید، از پنل آن کاربر کد بگیرید."
            );

            return response('ok');
        }

        // ۳) هر چیز غیر از کد ۸ رقمی → همان راهنمای شروع (تجربه کاربری بهتر از پیام خطای خام)
        if (! preg_match('/^\d{8}$/', $text)) {
            $bot->sendMessage($chatId, $this->welcomeMessage());

            return response('ok');
        }

        // ۴) اعتبارسنجی کد
        $code = BaleLinkCode::where('code', $text)->first();

        if (! $code || ! $code->isValid()) {
            $bot->sendMessage(
                $chatId,
                "❌ این کد نامعتبر یا منقضی شده است.\n\n"
                . "باز هم امتحان کنید:\n"
                . "۱. وارد پنل خود در سایت شوید.\n"
                . "۲. به بخش «اتصال ربات بله» بروید و کد جدید بگیرید.\n"
                . "۳. کد را همین‌جا ارسال کنید."
            );

            return response('ok');
        }

        // ۵) مصرف یک‌بارِ کد (اتمیک؛ در برابر ارسال همزمان مقاوم است)
        $consumed = BaleLinkCode::whereKey($code->getKey())
            ->whereNull('used_at')
            ->update(['used_at' => now(), 'linked_chat_id' => $chatId]);

        if (! $consumed) {
            $bot->sendMessage($chatId, "❌ این کد قبلاً استفاده شده است. از پنل سایت کد جدید بگیرید.");

            return response('ok');
        }

        // ۶) اتصال حساب و خوش‌آمد نهایی
        Admin::whereKey($code->admin_id)->update(['bale_chat_id' => (string) $chatId]);

        $admin = Admin::find($code->admin_id);

        $bot->sendMessage(
            $chatId,
            "🎉 <b>حساب شما با موفقیت متصل شد!</b>\n\n"
            . "👤 کاربر: " . e($admin?->name ?? '') . "\n\n"
            . "از این پس وضعیت تایید یا رد خبرهای شما به‌صورت خودکار از طریق همین ربات اطلاع‌رسانی می‌شود.\n\n"
            . "برای قطع اتصال، از پنل خود در سایت اقدام کنید."
        );

        Log::info('Bale account linked', ['admin_id' => $code->admin_id, 'chat_id' => $chatId]);

        return response('ok');
    }

    /**
     * تشخیص دستور شروع (/start یا «شروع» یا «استارت»).
     */
    protected function isStartCommand(string $text): bool
    {
        $normalized = mb_strtolower(trim($text), 'UTF-8');

        return in_array($normalized, ['/start', 'start', 'شروع', 'استارت', 'شروع ربات'], true);
    }

    /**
     * پیام خوش‌آمد + راهنمای گام‌به‌گام اتصال.
     */
    protected function welcomeMessage(): string
    {
        $botName = Setting::get('bale_bot_username');

        $botLine = $botName
            ? "🤖 ربات خبر <b>دیدبان شهر</b> (@" . e($botName) . ")\n\n"
            : "🤖 ربات خبر <b>دیدبان شهر</b>\n\n";

        return $botLine
            . "سلام! 👋 خوش آمدید.\n\n"
            . "این ربات نتیجه بررسی خبرهای شما را اطلاع‌رسانی می‌کند:\n"
            . "✅ تایید خبر\n"
            . "❌ رد خبر (همراه با دلیل)\n\n"
            . "برای اتصال حساب:\n"
            . "۱️⃣ وارد پنل خود در سایت شوید.\n"
            . "۲️⃣ به بخش <b>«اتصال ربات بله»</b> بروید و کد یک‌بارمصرف بگیرید.\n"
            . "۳️⃣ کد ۸ رقمی را همین‌جا ارسال کنید.\n\n"
            . "⏳ کد فقط ۱۵ دقیقه اعتبار دارد.";
    }
}
