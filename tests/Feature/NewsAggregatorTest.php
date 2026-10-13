<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\SourceChannel;
use App\Models\DestinationChannel;
use App\Models\Post;
use App\Services\Aggregator\ContentCleaner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsAggregatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_cleaner_removes_ads_and_mentions()
    {
        $cleaner = new ContentCleaner();

        $source = SourceChannel::create([
            'channel_link' => '@test_ch',
            'filter_keywords' => 'فروش ویژه، تخفیف',
            'remove_links' => true,
            'remove_tags' => true,
        ]);

        $raw = "خبر مهم شهر\n\nفروش ویژه محصولات با ۵۰٪ تخفیف\nhttps://t.me/shop/123\n@shop_admin";
        $result = $cleaner->clean($raw, $source);

        $this->assertTrue($result['is_ad']);

        $normal = "آتش‌سوزی در پارک شهر مهار شد.\n\nجزئیات بیشتر در سایت.";
        $normalResult = $cleaner->clean($normal, $source);

        $this->assertFalse($normalResult['is_ad']);
        $this->assertStringContainsString('آتش‌سوزی در پارک شهر مهار شد', $normalResult['cleaned']);
    }

    public function test_source_and_destination_creation()
    {
        $source = SourceChannel::create([
            'channel_link' => 'https://t.me/varzesh3',
            'fetch_interval_minutes' => 10,
        ]);

        $destination = DestinationChannel::create([
            'name' => 'ربات تلگرام',
            'platform' => 'telegram',
            'bot_token' => '123456:ABC-DEF',
            'channel_identifier' => '@my_channel',
        ]);

        $this->assertDatabaseHas('source_channels', ['channel_link' => 'https://t.me/varzesh3']);
        $this->assertDatabaseHas('destination_channels', ['channel_identifier' => '@my_channel']);
    }
}
