<?php

namespace App\Console\Commands;

use App\Services\Aggregator\NewsAggregatorManager;
use Illuminate\Console\Command;

class PollNewsSourcesCommand extends Command
{
    protected $signature = 'aggregator:poll';
    protected $description = 'Poll active social channels/sources for new articles and cross-publish';

    public function handle(NewsAggregatorManager $manager): int
    {
        $this->info('Starting news aggregation cycle...');
        $manager->pollSources();
        $this->info('News aggregation cycle completed.');
        return Command::SUCCESS;
    }
}
