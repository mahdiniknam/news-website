# News Aggregator Subsystem Design Spec

## 1. Goal & Architecture
Add an automated news aggregation, cleaning, deduplication, and cross-publishing system into the existing Laravel news application.

### Pipeline:
`Source Channel -> Fetch Raw Messages -> Deduplicate -> Download Media -> Clean & Format Content -> Create News Post -> Cross-Publish Destination -> Save Log`

## 2. Models & Schema

### `source_channels`
- `id`: PK
- `name`: string
- `platform`: string (telegram, bale, rubika, eitaa, rss)
- `identifier`: string (e.g. channel username `@varzesh3`)
- `is_active`: boolean default true
- `fetch_interval_minutes`: integer default 15
- `last_fetched_at`: timestamp nullable
- `configuration`: json nullable (api keys, specific session data)
- `cleanup_rules`: json nullable (strip words, regex patterns, custom mentions)
- `default_category_id`: foreignId('categories')->nullable()
- `default_author_id`: foreignId('admins')->nullable()

### `destination_channels`
- `id`: PK
- `name`: string
- `platform`: string (telegram, bale, rubika, eitaa)
- `identifier`: string (e.g. chat_id / channel username)
- `is_active`: boolean default true
- `bot_token`: text nullable (encrypted)
- `configuration`: json nullable
- `template_header`: text nullable
- `template_footer`: text nullable (custom hashtags, links, credits)

### `aggregated_messages`
- `id`: PK
- `source_channel_id`: foreignId('source_channels')
- `external_message_id`: string
- `platform`: string
- `raw_content`: longText nullable
- `cleaned_content`: longText nullable
- `media_urls`: json nullable
- `local_media_paths`: json nullable
- `status`: enum (pending, processing, published, skipped, failed, duplicate)
- `status_reason`: text nullable
- `post_id`: foreignId('posts')->nullable()
- `error_message`: text nullable
- `unique`: [`platform`, `source_channel_id`, `external_message_id`]

### `posts` (Existing Table Extension)
- `is_automated`: boolean default false
- `source_channel_id`: foreignId('source_channels')->nullable()
- `original_source_url`: string nullable

## 3. Interfaces
- `App\Services\Aggregator\Contracts\SourceConnectorInterface` (`fetchLatestMessages(SourceChannel $source): array`)
- `App\Services\Aggregator\Contracts\PublisherInterface` (`publish(DestinationChannel $destination, Post $post, array $media): bool`)
- `App\Services\Aggregator\ContentCleaner`: Deterministic regex/blacklist word filtering and mention replacement.
- `App\Services\Aggregator\DuplicateDetector`: Exact external message check + unique DB constraint.
