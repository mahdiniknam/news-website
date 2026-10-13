<?php

namespace App\Services\Aggregator\Contracts;

use App\Models\DestinationChannel;
use App\Models\Post;

interface PublisherInterface
{
    /**
     * Publish the post to the specified destination channel.
     *
     * @param DestinationChannel $destination
     * @param Post $post
     * @param array<string> $mediaPaths
     * @return bool
     */
    public function publish(DestinationChannel $destination, Post $post, array $mediaPaths = []): bool;
}
