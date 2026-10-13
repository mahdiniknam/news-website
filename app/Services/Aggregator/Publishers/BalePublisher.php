<?php

namespace App\Services\Aggregator\Publishers;

use App\Models\DestinationChannel;
use App\Models\Post;
use App\Services\BaleBotService;
use App\Services\Aggregator\Contracts\PublisherInterface;

class BalePublisher implements PublisherInterface
{
    protected BaleBotService $baleBot;

    public function __construct(BaleBotService $baleBot)
    {
        $this->baleBot = $baleBot;
    }

    public function publish(DestinationChannel $destination, Post $post, array $mediaPaths = []): bool
    {
        $chatId = $destination->channel_identifier;
        $websiteUrl = route('post.show', $post->slug);
        $text = "<b>{$post->title}</b>\n\n".strip_tags($post->content)."\n\n🔗 <a href=\"{$websiteUrl}\">مشاهده در وب‌سایت</a>";
        return $this->baleBot->sendMessage($chatId, $text);
    }
}
