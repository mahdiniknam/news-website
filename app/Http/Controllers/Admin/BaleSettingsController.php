<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\BaleBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * تنظیمات ربات بله در پنل ادمین.
 * فقط super-admin اجازه تغییر تنظیمات ربات را دارد.
 */
class BaleSettingsController extends Controller
{
    public function __construct()
    {
        // دسترسی فقط برای super-admin (از طریق middleware نقش در روت هم اعمال می‌شود)
    }

    public function index(BaleBotService $bot)
    {
        $tokenSet = $bot->isConfigured();
        $tokenSource = \App\Models\Setting::query()->find('bale_bot_token')?->value !== null ? 'db' : 'env';

        $me = $tokenSet ? $bot->getMe() : ['ok' => false];
        $webhook = $bot->getWebhookInfo();

        // ادمین‌هایی که از طریق کد اتصال، بله‌شان وصل شده
        $linkedAdmins = Admin::whereNotNull('bale_chat_id')
            ->with('roles:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'bale_chat_id']);

        $webhookUrl = url()->baleWebhook();

        // تعداد کدهای فعال
        $activeCodes = \App\Models\BaleLinkCode::whereNull('used_at')
            ->where('expires_at', '>', now())
            ->count();

        return view('admin.pages.bale-settings', [
            'tokenSet' => $tokenSet,
            'tokenSource' => $tokenSource,
            'botUsername' => $me['ok'] ? $me['username'] : null,
            'botError' => $me['ok'] ? null : ($me['error'] ?? null),
            'webhookUrl' => $webhook['result']['url'] ?? null,
            'webhookExpectedUrl' => $webhookUrl,
            'webhookSecretSet' => filled(config('services.bale.webhook_secret')),
            'linkedAdmins' => $linkedAdmins,
            'activeCodes' => $activeCodes,
        ]);
    }

    public function store(Request $request, BaleBotService $bot)
    {
        $validated = $request->validate(
            [
                'bot_token' => ['nullable', 'string', 'regex:/^\d{5,}:[\w-]{20,}$/'],
                'remove_token' => ['nullable', 'boolean'],
                'set_webhook' => ['nullable', 'boolean'],
            ],
            [
                'bot_token.regex' => 'فرمت توکن ربات صحیح نیست (نمونه: 123456789:AbCdEf...) — توکن را از @BotFather بگیرید',
            ]
        );

        // حذف توکن
        if (! empty($validated['remove_token'])) {
            BaleBotService::storeToken(null);

            return back()->with('success', 'توکن ربات حذف شد.');
        }

        if (isset($validated['bot_token'])) {
            $token = trim($validated['bot_token']);

            // تست توکن قبل از ذخیره
            $testResult = $this->testToken($token);

            if (! $testResult['ok']) {
                return back()
                    ->withErrors(['bot_token' => 'توکن نامعتبر است: ' . ($testResult['error'] ?? 'خطای نامشخص')])
                    ->withInput();
            }

            BaleBotService::storeToken($token);

            // ذخیره نام کاربری ربات برای نمایش در پنل و پیام‌های راهنما
            \App\Models\Setting::set('bale_bot_username', $testResult['username']);

            // ثبت webhook با همان توکن
            if (! empty($validated['set_webhook'])) {
                $webhookUrl = url()->baleWebhook();
                $wh = $bot->setWebhook($webhookUrl);

                if (! $wh['ok']) {
                    Log::warning('Bale webhook setup failed: ' . ($wh['error'] ?? ''));

                    return back()->with(
                        'success',
                        'توکن ذخیره شد اما ثبت webhook ناموفق بود: ' . ($wh['error'] ?? '')
                    );
                }
            }

            return back()->with('success', 'توکن ربات با موفقیت ذخیره شد. نام ربات: @' . $testResult['username']);
        }

        return back()->with('error', 'تغییری برای ذخیره وجود ندارد.');
    }

    /**
     * تست صحت توکن با getMe (بدون ذخیره).
     */
    protected function testToken(string $token): array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->get(rtrim(config('services.bale.api_base', 'https://tapi.bale.ai'), '/') . "/bot{$token}/getMe");

            if ($response->successful() && ($response->json('ok') ?? false)) {
                return ['ok' => true, 'username' => $response->json('result.username')];
            }

            return ['ok' => false, 'error' => $response->json('description') ?? 'پاسخ نامعتبر'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
