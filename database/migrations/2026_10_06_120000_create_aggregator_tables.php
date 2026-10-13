<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old tables if necessary or adjust schema for new simplified aggregator
        Schema::dropIfExists('aggregated_messages');
        Schema::dropIfExists('destination_channels');
        Schema::dropIfExists('source_channels');

        Schema::create('source_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('platform')->default('telegram');
            $table->string('channel_link'); // e.g. https://t.me/varzesh3 or @varzesh3
            $table->boolean('is_active')->default(true);
            $table->integer('fetch_interval_minutes')->default(10);
            $table->text('filter_keywords')->nullable(); // comma or newline separated
            $table->boolean('remove_links')->default(true);
            $table->boolean('remove_tags')->default(true);
            $table->timestamp('last_fetched_at')->nullable();
            $table->timestamps();
        });

        Schema::create('destination_channels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('platform')->default('telegram');
            $table->text('bot_token');
            $table->string('channel_identifier'); // @my_channel or -100xxx
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('aggregated_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_channel_id')->constrained('source_channels')->cascadeOnDelete();
            $table->string('platform')->default('telegram');
            $table->string('external_message_id');
            $table->longText('raw_content')->nullable();
            $table->longText('cleaned_content')->nullable();
            $table->json('media_urls')->nullable();
            $table->json('local_media_paths')->nullable();
            $table->string('status', 30)->default('pending'); // published, skipped, failed
            $table->text('status_reason')->nullable();
            $table->foreignId('post_id')->nullable()->constrained('posts')->nullOnDelete();
            $table->timestamps();

            $table->unique(['platform', 'source_channel_id', 'external_message_id'], 'agg_msg_uniq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aggregated_messages');
        Schema::dropIfExists('destination_channels');
        Schema::dropIfExists('source_channels');
    }
};
