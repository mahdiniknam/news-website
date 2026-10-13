<?php

namespace App\Services\Aggregator;

use App\Models\AggregatedMessage;
use App\Models\DestinationChannel;
use App\Models\Post;
use App\Models\Setting;
use App\Models\SourceChannel;
use App\Services\Aggregator\Connectors\Telegram\TelegramSourceConnector;
use App\Services\Aggregator\Publishers\BalePublisher;
use App\Services\Aggregator\Publishers\TelegramPublisher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NewsAggregatorManager
{
    protected ContentCleaner $cleaner;

    public function __construct(ContentCleaner $cleaner)
    {
        $this->cleaner = $cleaner;
    }

    public function pollSources(): void
    {
        $sources = SourceChannel::where('is_active', true)->get();

        foreach ($sources as $source) {
            try {
                $connector = $this->resolveConnector($source->platform);
                if (!$connector) continue;

                $messages = $connector->fetchLatestMessages($source);

                if (empty($messages)) {
                    $source->update(['last_fetched_at' => now()]);
                    continue;
                }

                foreach ($messages as $msgData) {
                    $externalId = (string) $msgData['external_id'];
                    $platform = $source->platform;
                    $rawText = $msgData['text'] ?? '';

                    if (AggregatedMessage::where('platform', $platform)
                        ->where('source_channel_id', $source->id)
                        ->where('external_message_id', $externalId)->exists()) {
                        continue;
                    }

                    $aggMessage = AggregatedMessage::create([
                        'source_channel_id' => $source->id,
                        'platform' => $platform,
                        'external_message_id' => $externalId,
                        'raw_content' => $rawText,
                        'media_urls' => $msgData['media_urls'] ?? [],
                        'status' => 'processing',
                    ]);

                    $cleanResult = $this->cleaner->clean($rawText, $source);
                    if ($cleanResult['is_ad']) {
                        $aggMessage->update(['status' => 'skipped', 'status_reason' => $cleanResult['ad_reason']]);
                        continue;
                    }

                    $cleanedText = $cleanResult['cleaned'];
                    if (mb_strlen(trim($cleanedText)) < 10) {
                        $cleanedText = trim($rawText);
                        $cleanedText = $this->cleaner->clean($cleanedText, $source)['cleaned'];
                    }
                    if (empty(trim($cleanedText)) && empty($msgData['media_urls'])) {
                        $aggMessage->update(['status' => 'skipped', 'status_reason' => 'محتوای خالی پس از پاکسازی']);
                        continue;
                    }
                    if (empty(trim($cleanedText)) && !empty($msgData['media_urls'])) {
                        $cleanedText = 'گزارش تصویری';
                    }
                    $aggMessage->update(['cleaned_content' => $cleanedText]);

                    $localMediaPaths = $this->downloadAllMedia($msgData['media_urls'] ?? []);
                    $featuredImagePath = null;
                    foreach ($localMediaPaths as $lp) {
                        $ext = strtolower(pathinfo($lp, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg','jpeg','png','webp','gif'])) { $featuredImagePath = $lp; break; }
                    }
                    if (!$featuredImagePath && !empty($localMediaPaths)) $featuredImagePath = $localMediaPaths[0];
                    $aggMessage->update(['local_media_paths' => $localMediaPaths]);

                    $title = $this->extractTitle($cleanedText);

                    $authorId = (int) (Setting::get('aggregator_default_author_id') ?? 1);
                    $categoryId = Setting::get('aggregator_default_category_id');
                    $categoryId = $categoryId ? (int)$categoryId : null;
                    $tagIds = json_decode(Setting::get('aggregator_default_tag_ids', '[]'), true) ?: [];
                    if (!$categoryId) $categoryId = \App\Models\Category::where('status', 1)->ordered()->first()?->id;
                    if (!\App\Models\Admin::where('id', $authorId)->exists()) $authorId = \App\Models\Admin::first()?->id ?? 1;

                    $mediaTags = $this->buildMediaTags($localMediaPaths);
                    $postContent = $mediaTags . '<div>' . nl2br(e($cleanedText)) . '</div>';

                    try {
                        $post = DB::transaction(function () use ($authorId, $categoryId, $title, $postContent, $featuredImagePath, $source) {
                            return Post::create([
                                'author_id' => $authorId,
                                'category_id' => $categoryId,
                                'title' => $title,
                                'content' => $postContent,
                                'featured_image' => $featuredImagePath,
                                'status' => 'published',
                                'type' => 'news',
                                'published_at' => now(),
                                'is_automated' => true,
                                'source_channel_id' => $source->id,
                            ]);
                        });
                    } catch (\Illuminate\Database\QueryException $e) {
                        if (str_contains($e->getMessage(), 'posts_slug_unique') || $e->getCode() == 23000) {
                            $title = $title . ' — ' . $externalId;
                            $post = Post::create([
                                'author_id' => $authorId,
                                'category_id' => $categoryId,
                                'title' => $title,
                                'content' => $postContent,
                                'featured_image' => $featuredImagePath,
                                'status' => 'published',
                                'type' => 'news',
                                'published_at' => now(),
                                'is_automated' => true,
                                'source_channel_id' => $source->id,
                            ]);
                        } else {
                            throw $e;
                        }
                    }

                    if (!empty($tagIds)) $post->tags()->sync($tagIds);
                    $aggMessage->update(['status' => 'published', 'post_id' => $post->id]);

                    $this->publishToDestinations($post, $localMediaPaths);
                }

                $source->update(['last_fetched_at' => now()]);
            } catch (\Throwable $e) {
                Log::error("Error polling source {$source->id}: ".$e->getMessage(), ['exception' => $e]);
                try {
                    AggregatedMessage::create([
                        'source_channel_id' => $source->id,
                        'platform' => $source->platform,
                        'external_message_id' => 'error_'.time().'_'.Str::random(4),
                        'raw_content' => null,
                        'status' => 'failed',
                        'status_reason' => mb_substr($e->getMessage(),0,500),
                    ]);
                } catch (\Throwable) {}
            }
        }
    }

    private function downloadAllMedia(array $urls): array
    {
        $maxMb = (int) (Setting::get('aggregator_max_video_mb') ?? 50);
        if ($maxMb <= 0) $maxMb = 50;
        $maxBytes = $maxMb * 1024 * 1024;

        $paths = [];
        foreach ($urls as $mediaUrl) {
            $isVideo = (bool) preg_match('/\.mp4(\?|$)/i', $mediaUrl);

            if ($isVideo) {
                try {
                    $head = Http::timeout(10)->withHeaders(['User-Agent'=>'Mozilla/5.0','Referer'=>'https://t.me/'])->head($mediaUrl);
                    $len = (int) ($head->header('Content-Length') ?? 0);
                    if ($len > $maxBytes) {
                        Log::info("Skip video >{$maxMb}MB (Content-Length {$len}) for {$mediaUrl}");
                        continue;
                    }
                } catch (\Throwable) {}
            }

            try {
                $resp = Http::timeout($isVideo ? 90 : 30)->withHeaders(['User-Agent'=>'Mozilla/5.0','Referer'=>'https://t.me/'])->get($mediaUrl);
                if (!$resp->successful()) {
                    Log::warning("Media download HTTP {$resp->status()} for {$mediaUrl}");
                    continue;
                }
                $body = $resp->body();
                if (strlen($body) < 1000) {
                    Log::warning("Media download too small (".strlen($body)."b) for {$mediaUrl}");
                    continue;
                }
                if ($isVideo && strlen($body) > $maxBytes) {
                    Log::info("Skip video body >{$maxMb}MB (".strlen($body)."b) for {$mediaUrl}");
                    continue;
                }
                $ctype = strtolower($resp->header('Content-Type') ?? '');
                $ext = 'jpg';
                if ($isVideo || str_contains($ctype, 'mp4') || str_contains($ctype, 'video')) $ext = 'mp4';
                elseif (str_contains($ctype, 'webm')) $ext = 'webm';
                elseif (str_contains($ctype, 'png')) $ext = 'png';
                elseif (str_contains($ctype, 'gif')) $ext = 'gif';
                elseif (str_contains($ctype, 'webp')) $ext = 'webp';
                elseif (preg_match('/\.(\w+)(\?|$)/', $mediaUrl, $m)) {
                    $guess = strtolower($m[1]);
                    if (in_array($guess, ['jpg','jpeg','png','gif','webp','mp4','webm','mov'])) $ext = $guess;
                }
                $path = 'posts/agg_'.Str::random(16).'.'.$ext;
                Storage::disk('public')->put($path, $body);
                $paths[] = $path;
            } catch (\Throwable $e) {
                Log::warning("Media download exception for {$mediaUrl}: ".$e->getMessage());
            }
        }
        return $paths;
    }

    private function extractTitle(string $cleanedText): string
    {
        $lines = array_filter(array_map('trim', explode("\n", $cleanedText)));
        $title = $lines[array_key_first($lines)] ?? '';
        $title = trim($title);
        if (mb_strlen($title) < 5 || mb_strlen($title) > 180) {
            $title = mb_substr(strip_tags($cleanedText), 0, 90).'…';
        }
        return $title ?: 'خبر فوری — '.now()->format('Y/m/d H:i');
    }

    private function buildMediaTags(array $localMediaPaths): string
    {
        $tags = '';
        foreach ($localMediaPaths as $mp) {
            $url = Storage::disk('public')->url($mp);
            $ext = strtolower(pathinfo($mp, PATHINFO_EXTENSION));
            if (in_array($ext, ['mp4','mov','webm','avi','mkv'])) {
                $tags .= '<video controls preload="metadata" style="width:100%;max-width:100%;margin:12px 0;" src="'.e($url).'"></video>';
            } else {
                $tags .= '<p><img src="'.e($url).'" style="max-width:100%;height:auto;display:block;margin:12px 0;" alt=""></p>';
            }
        }
        return $tags;
    }

    public function publishToDestinations(Post $post, array $mediaPaths = []): void
    {
        $destinations = DestinationChannel::where('is_active', true)->get();
        foreach ($destinations as $destination) {
            try {
                $publisher = $this->resolvePublisher($destination->platform);
                if ($publisher) $publisher->publish($destination, $post, $mediaPaths);
            } catch (\Throwable $e) {
                Log::error("Error publishing to destination {$destination->id}: ".$e->getMessage());
            }
        }
    }

    protected function resolveConnector(string $platform)
    {
        return match ($platform) {
            'telegram' => new TelegramSourceConnector(),
            default => new TelegramSourceConnector(),
        };
    }

    protected function resolvePublisher(string $platform)
    {
        return match ($platform) {
            'telegram' => new TelegramPublisher(),
            'bale' => app(BalePublisher::class),
            default => new TelegramPublisher(),
        };
    }
}
