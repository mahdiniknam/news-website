<?php

namespace App\Services\Aggregator\Connectors\Telegram;

use App\Models\SourceChannel;
use App\Services\Aggregator\Contracts\SourceConnectorInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramSourceConnector implements SourceConnectorInterface
{
    public function fetchLatestMessages(SourceChannel $source): array
    {
        $botToken = config('services.telegram.bot_token') ?? env('TELEGRAM_BOT_TOKEN');
        $rawLink = trim($source->channel_link ?? '');
        $handle = $this->normalizeChannelIdentifier($rawLink);

        $results = [];

        if (!empty($botToken)) {
            $botResults = $this->fetchViaGetUpdates($botToken, $handle, $rawLink, $source->id);
            $results = array_merge($results, $botResults);
        }

        $scraped = $this->fetchViaScrape($handle);
        foreach ($scraped as $m) {
            $exists = false;
            foreach ($results as $r) {
                if ($r['external_id'] === $m['external_id']) { $exists = true; break; }
            }
            if (!$exists) $results[] = $m;
        }

        if (empty($results)) {
            Log::info("TelegramSourceConnector: no messages for {$handle} (bot empty + scrape empty or blocked)");
        }

        $results = collect($results)->sortBy('external_id')->values()->all();
        return array_slice($results, -10);
    }

    private function fetchViaGetUpdates(string $botToken, string $handle, string $rawLink, int $sourceId): array
    {
        try {
            $response = Http::timeout(15)->get("https://api.telegram.org/bot{$botToken}/getUpdates");
            if (!$response->successful() || !$response->json('ok')) {
                $conflict = $response->json('error_code') === 409;
                if ($conflict) {
                    Log::warning("Telegram getUpdates 409 for source {$sourceId} — webhook active, trying deleteWebhook");
                    try { Http::timeout(10)->get("https://api.telegram.org/bot{$botToken}/deleteWebhook?drop_pending_updates=false"); } catch (\Throwable) {}
                    $response = Http::timeout(15)->get("https://api.telegram.org/bot{$botToken}/getUpdates");
                    if (!$response->successful() || !$response->json('ok')) return [];
                } else {
                    return [];
                }
            }
            $updates = $response->json('result') ?? [];
            $messages = [];
            foreach ($updates as $update) {
                $msg = $update['channel_post'] ?? $update['message'] ?? null;
                if (!$msg) continue;
                $chatUsername = '@' . ltrim($msg['chat']['username'] ?? '', '@');
                $messageChatId = (string)($msg['chat']['id'] ?? '');
                if ($chatUsername !== $handle && $messageChatId !== $handle && $messageChatId !== $rawLink) continue;
                    if (isset($msg['voice']) || isset($msg['audio']) || isset($msg['sticker'])) {
                        continue;
                    }
                    $text = $msg['text'] ?? $msg['caption'] ?? '';
                    $messageId = (string)$msg['message_id'];
                    $mediaUrls = [];
                    if (isset($msg['photo']) && is_array($msg['photo'])) {
                        $largest = end($msg['photo']);
                        $fileId = $largest['file_id'] ?? null;
                        if ($fileId) {
                            $fileUrl = $this->getTelegramFileUrl($botToken, $fileId);
                            if ($fileUrl) $mediaUrls[] = $fileUrl;
                        }
                    }
                    if (isset($msg['video']['file_id'])) {
                        $fileUrl = $this->getTelegramFileUrl($botToken, $msg['video']['file_id']);
                        if ($fileUrl) $mediaUrls[] = $fileUrl;
                    }
                    if (isset($msg['animation']['file_id'])) {
                        $fileUrl = $this->getTelegramFileUrl($botToken, $msg['animation']['file_id']);
                        if ($fileUrl) $mediaUrls[] = $fileUrl;
                    }
                    if (!empty($text) || !empty($mediaUrls)) {
                        $messages[] = ['external_id'=>$messageId,'text'=>$text,'media_urls'=>$mediaUrls,'timestamp'=>$msg['date'] ?? time()];
                    }
            }
            return $messages;
        } catch (\Throwable $e) {
            Log::warning("Telegram getUpdates failed: ".$e->getMessage());
            return [];
        }
    }

    private function fetchViaScrape(string $handle): array
    {
        $username = ltrim($handle, '@');
        if ($username === '') return [];
        try {
            $url = "https://t.me/s/{$username}";
            $resp = Http::timeout(15)->withHeaders(['User-Agent'=>'Mozilla/5.0'])->get($url);
            if (!$resp->successful()) return [];
            $html = $resp->body();

            $parts = preg_split('/<div class="tgme_widget_message_wrap/', $html);
            $results = [];
            foreach (array_slice($parts, 1) as $part) {
                $chunk = '<div class="tgme_widget_message_wrap' . $part;

                if (!preg_match('/data-post="[^"]+\/(\d+)"/', $chunk, $pm)) continue;
                $postId = $pm[1];

                if (str_contains($chunk, 'tgme_widget_message_voice') || str_contains($chunk, 'tgme_widget_message_audio') || str_contains($chunk, 'tgme_widget_message_sticker')) {
                    continue;
                }
                $text = '';
                if (preg_match('/tgme_widget_message_text[^>]*>(.*?)<\/div>/s', $chunk, $tm)) {
                    $rawHtml = $tm[1];
                    $rawHtml = preg_replace('/<br\s*\/?>/i', "\n", $rawHtml);
                    $text = html_entity_decode(strip_tags($rawHtml), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $text = trim(preg_replace('/[ \t]+/u', ' ', $text));
                    $text = preg_replace("/\n{3,}/", "\n\n", $text);
                    $text = trim($text);
                }

                $mediaUrls = [];
                preg_match_all("/background-image:url\('([^']+)'\)/", $chunk, $bgMatches);
                foreach ($bgMatches[1] as $bgUrl) {
                    $bgUrl = html_entity_decode($bgUrl, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    if (str_contains($bgUrl, 'telegram.org/img/emoji')) continue;
                    if (str_contains($bgUrl, 'cdn') || str_contains($bgUrl, 'telesco')) {
                        $bgUrl = str_replace('\\', '', $bgUrl);
                        if (!str_starts_with($bgUrl, 'http')) $bgUrl = 'https:' . $bgUrl;
                        $mediaUrls[] = $bgUrl;
                    }
                }

                if (preg_match_all('/<video[^>]+src="([^"]+)"/i', $chunk, $vmAll)) {
                    foreach ($vmAll[1] as $vSrc) {
                        $vUrl = html_entity_decode($vSrc, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        if (str_starts_with($vUrl, 'https://')) $mediaUrls[] = $vUrl;
                    }
                }

                if (preg_match('/tgme_widget_message_video_thumb[^>]*style="[^"]*background-image:url\(\'([^\']+)\'\)/i', $chunk, $vtm)) {
                    $thumb = html_entity_decode($vtm[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $thumb = str_replace('\\', '', $thumb);
                    if (!str_starts_with($thumb, 'http')) $thumb = 'https:' . $thumb;
                    if (!in_array($thumb, $mediaUrls) && (str_contains($thumb,'cdn')||str_contains($thumb,'telesco'))) {
                        array_unshift($mediaUrls, $thumb);
                    }
                }

                if (mb_strlen($text) < 10 && empty($mediaUrls)) continue;

                $results[] = ['external_id'=>$postId,'text'=>$text,'media_urls'=>$mediaUrls,'timestamp'=>time()];
            }
            return $results;
        } catch (\Throwable $e) {
            Log::warning("Telegram scrape failed for {$username}: ".$e->getMessage());
            return [];
        }
    }

    private function normalizeChannelIdentifier(string $link): string
    {
        $link = trim($link);
        if ($link === '') return '';
        if (is_numeric(ltrim($link, '-'))) return $link;
        if (str_starts_with($link, '@')) return $link;
        if (str_contains($link, 't.me/')) {
            $parts = explode('t.me/', $link);
            $handle = trim(end($parts), '/ ');
            $handle = explode('?', $handle)[0];
            $handle = explode('/', $handle)[0];
            return '@' . ltrim($handle, '@');
        }
        return '@' . ltrim($link, '@');
    }

    protected function getTelegramFileUrl(string $botToken, string $fileId): ?string
    {
        try {
            $resp = Http::get("https://api.telegram.org/bot{$botToken}/getFile?file_id={$fileId}");
            if ($resp->successful() && $resp->json('ok')) {
                $filePath = $resp->json('result.file_path');
                return "https://api.telegram.org/file/bot{$botToken}/{$filePath}";
            }
        } catch (\Throwable) {}
        return null;
    }
}
