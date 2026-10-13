<?php

namespace App\Services\Aggregator\Contracts;

use App\Models\SourceChannel;

interface SourceConnectorInterface
{
    /**
     * Fetch the latest unread messages from a configured source.
     *
     * @param SourceChannel $source
     * @return array<int, array{external_id: string, text: string, media_urls: array<string>, timestamp: ?int}>
     */
    public function fetchLatestMessages(SourceChannel $source): array;
}
