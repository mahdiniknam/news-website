<?php

namespace App\Services\Aggregator\Publishers;

use App\Models\DestinationChannel;
use App\Models\Post;
use App\Services\Aggregator\Contracts\PublisherInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TelegramPublisher implements PublisherInterface
{
    public function publish(DestinationChannel $destination, Post $post, array $mediaPaths = []): bool
    {
        $botToken = $destination->bot_token;
        $chatId = $destination->channel_identifier;
        if (empty($botToken) || empty($chatId)) return false;

        $websiteUrl = route('post.show', $post->slug);
        $caption = "<b>".e($post->title)."</b>\n\n".strip_tags($post->content)."\n\n🔗 <a href=\"{$websiteUrl}\">مشاهده در وب‌سایت</a>";
        $caption = mb_substr($caption, 0, 1024);

        $validPaths = array_values(array_filter($mediaPaths, fn($p) => is_file(Storage::disk('public')->path($p))));

        if (count($validPaths) >= 1) {
            $first = $validPaths[0];
            $absFirst = Storage::disk('public')->path($first);
            $extFirst = strtolower(pathinfo($absFirst, PATHINFO_EXTENSION));
            $isVideoFirst = in_array($extFirst, ['mp4','mov','avi','mkv','webm']);
            $method = $isVideoFirst ? 'sendVideo' : 'sendPhoto';
            $field = $isVideoFirst ? 'video' : 'photo';

            try {
                $resp = Http::timeout(60)
                    ->attach($field, file_get_contents($absFirst), basename($absFirst))
                    ->post("https://api.telegram.org/bot{$botToken}/{$method}", [
                        'chat_id' => $chatId,
                        'caption' => $caption,
                        'parse_mode' => 'HTML',
                    ]);
                if ($resp->successful() && ($resp->json('ok') ?? false)) {
                    $remaining = array_slice($validPaths, 1);
                    foreach (array_chunk($remaining, 10) as $chunk) {
                        $this->sendMediaGroup($botToken, $chatId, $chunk);
                    }
                    return true;
                }
                Log::warning("Telegram {$method} failed: ".$resp->body());

                if (count($validPaths) >= 2) {
                    $ok = $this->sendMediaGroup($botToken, $chatId, $validPaths, $caption);
                    if ($ok) return true;
                }
            } catch (\Throwable $e) {
                Log::warning("Telegram {$method} exception: ".$e->getMessage());
            }
        }

        if (count($validPaths) >= 2) {
            $ok = $this->sendMediaGroup($botToken, $chatId, $validPaths, $caption);
            if ($ok) return true;
        }

        $text = "<b>".e($post->title)."</b>\n\n".strip_tags($post->content)."\n\n🔗 <a href=\"{$websiteUrl}\">مشاهده در وب‌سایت</a>";
        try {
            $resp = Http::timeout(15)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => mb_substr($text, 0, 4000),
                'parse_mode' => 'HTML',
                'link_preview_options' => ['is_disabled' => false],
            ]);
            if ($resp->successful() && ($resp->json('ok') ?? false)) return true;
            Log::warning("Telegram sendMessage failed: ".$resp->body());
            return false;
        } catch (\Throwable $e) {
            Log::error("TelegramPublisher sendMessage failed: ".$e->getMessage());
            return false;
        }
    }

    private function sendMediaGroup(string $botToken, string $chatId, array $paths, string $caption = ''): bool
    {
        try {
            $media = [];
            foreach ($paths as $idx => $p) {
                $ap = Storage::disk('public')->path($p);
                if (!is_file($ap)) continue;
                $ext = strtolower(pathinfo($ap, PATHINFO_EXTENSION));
                $type = in_array($ext, ['mp4','mov','avi','mkv','webm']) ? 'video' : 'photo';
                $item = ['type'=>$type, 'media'=>"attach://file{$idx}", 'parse_mode'=>'HTML'];
                if ($idx === 0 && $caption !== '') $item['caption'] = mb_substr($caption, 0, 1024);
                $media[] = $item;
            }
            if (empty($media)) return false;
            $req = Http::timeout(90);
            foreach ($paths as $idx => $p) {
                $ap = Storage::disk('public')->path($p);
                if (!is_file($ap)) continue;
                $req = $req->attach("file{$idx}", file_get_contents($ap), basename($ap));
            }
            $resp = $req->post("https://api.telegram.org/bot{$botToken}/sendMediaGroup", [
                'chat_id' => $chatId,
                'media' => json_encode($media, JSON_UNESCAPED_UNICODE),
            ]);
            if ($resp->successful() && ($resp->json('ok') ?? false)) return true;
            Log::warning("Telegram sendMediaGroup failed: ".$resp->body());
            return false;
        } catch (\Throwable $e) {
            Log::warning("Telegram sendMediaGroup exception: ".$e->getMessage());
            return false;
        }
    }
}
