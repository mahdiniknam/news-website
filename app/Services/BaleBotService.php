<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * کلاینت سبک برای Bot API پیام‌رسان بله (سازگار با Bot API تلگرام).
 *
 * مستندات: https://docs.bale.ai
 * اندپوینت: https://tapi.bale.ai/bot<token>/METHOD_NAME
 *
 * توکن ربات در پنل ادمین (تنظیمات ربات بله) ذخیره می‌شود و
 * به‌صورت رمزنگاری‌شده در جدول settings نگه‌داری می‌شود.
 * مقدار BALE_BOT_TOKEN در .env فقط به‌عنوان مقدار اولیه (fallback) استفاده می‌شود.
 */
class BaleBotService
{
    protected string $apiBase;

    protected int $timeout;

    public function __construct()
    {
        $this->apiBase = rtrim(config('services.bale.api_base', 'https://tapi.bale.ai'), '/');
        $this->timeout = (int) config('services.bale.timeout', 10);
    }

    /**
     * توکن فعال ربات: اول از دیتابیس (پنل ادمین)، بعد از .env.
     */
    public function token(): ?string
    {
        $token = Setting::get('bale_bot_token');

        if (is_string($token) && $token !== '') {
            return $token;
        }

        $envToken = (string) config('services.bale.bot_token');

        return $envToken !== '' ? $envToken : null;
    }

    public function isConfigured(): bool
    {
        return $this->token() !== null;
    }

    /**
     * ذخیره توکن در دیتابیس (رمزنگاری‌شده). null یعنی حذف توکن.
     */
    public static function storeToken(?string $token): void
    {
        Setting::set('bale_bot_token', $token !== null && $token !== '' ? $token : null, encrypt: true);
    }

    /**
     * نام کاربری ربات با getMe؛ برای تست صحت توکن در پنل ادمین.
     *
     * @return array{ok: bool, username?: string, error?: string}
     */
    public function getMe(): array
    {
        $token = $this->token();

        if ($token === null) {
            return ['ok' => false, 'error' => 'توکن ربات تنظیم نشده است'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->apiBase}/bot{$token}/getMe");

            if ($response->successful() && ($response->json('ok') ?? false)) {
                return [
                    'ok' => true,
                    'username' => $response->json('result.username'),
                ];
            }

            return [
                'ok' => false,
                'error' => $response->json('description') ?? 'پاسخ نامعتبر از سرور بله',
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * ثبت webhook برای دریافت پیام‌های ربات (کدهای اتصال).
     */
    public function setWebhook(string $url): array
    {
        $token = $this->token();

        if ($token === null) {
            return ['ok' => false, 'error' => 'توکن ربات تنظیم نشده است'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->asJson()
                ->post("{$this->apiBase}/bot{$token}/setWebhook", [
                    'url' => $url,
                    'allowed_updates' => ['message'],
                ]);

            if ($response->successful() && ($response->json('ok') ?? false)) {
                return ['ok' => true];
            }

            return ['ok' => false, 'error' => $response->json('description') ?? 'خطا در ثبت webhook'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function getWebhookInfo(): array
    {
        $token = $this->token();

        if ($token === null) {
            return ['ok' => false];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->apiBase}/bot{$token}/getWebhookInfo");

            if ($response->successful() && ($response->json('ok') ?? false)) {
                return ['ok' => true, 'result' => $response->json('result')];
            }

            return ['ok' => false];
        } catch (\Throwable) {
            return ['ok' => false];
        }
    }

    /**
     * ارسال پیام متنی (HTML) به یک chat_id.
     */
    public function sendMessage(int|string $chatId, string $html): bool
    {
        $token = $this->token();

        if ($token === null) {
            Log::warning('Bale bot token is not configured; message not sent.', [
                'chat_id' => $chatId,
            ]);

            return false;
        }

        try {
            $response = Http::timeout($this->timeout)
                ->asJson()
                ->post("{$this->apiBase}/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $html,
                    'parse_mode' => 'HTML',
                    'link_preview_options' => ['is_disabled' => true],
                ]);

            if ($response->successful() && ($response->json('ok') ?? false)) {
                return true;
            }

            Log::error('Bale sendMessage failed.', [
                'chat_id' => $chatId,
                'status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 500),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('Bale sendMessage exception: ' . $e->getMessage(), [
                'chat_id' => $chatId,
            ]);

            return false;
        }
    }
}
