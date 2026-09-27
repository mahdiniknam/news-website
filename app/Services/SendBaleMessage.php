<?php

namespace App\Services;

use App\Models\BotNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * ارسال پیام بله در صف اجرا می‌شود تا کندی API سایت را بلاک نکند.
 * در آرشیو (bot_notifications) ثبت می‌شود تا ارسال تکراری رخ ندهد.
 */
class SendBaleMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 60];

    public function __construct(
        protected string $chatId,
        protected string $text,
        protected string $subject,
        protected string $notifiableType,
        protected ?int $notifiableId,
        protected ?int $postId,
    ) {}

    public function handle(BaleBotService $bot): void
    {
        // جلوگیری از ارسال تکراری در retry ها (به تفکیک پست)
        $alreadySent = BotNotification::where('chat_id', $this->chatId)
            ->where('subject', $this->subject)
            ->where('notifiable_id', $this->notifiableId)
            ->when($this->postId !== null, function ($q) {
                $q->where('payload', 'like', '%"post_id":' . $this->postId . ',%');
            })
            ->where('status', 'sent')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($alreadySent) {
            return;
        }

        $ok = $bot->sendMessage($this->chatId, $this->text);

        BotNotification::create([
            'chat_id' => $this->chatId,
            'subject' => $this->subject,
            'notifiable_type' => $this->notifiableType,
            'notifiable_id' => $this->notifiableId,
            'payload' => json_encode([
                'post_id' => $this->postId,
                'text_length' => mb_strlen($this->text),
            ], JSON_UNESCAPED_UNICODE),
            'status' => $ok ? 'sent' : 'failed',
            'error_message' => $ok ? null : 'sendMessage failed',
        ]);

        if (! $ok) {
            // برای retry بعدی پرتاب می‌شود
            throw new \RuntimeException('Bale sendMessage failed');
        }
    }
}
