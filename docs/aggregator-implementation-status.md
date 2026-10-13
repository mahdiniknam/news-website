# Automated News Aggregation & Publishing Subsystem - MVP Status

## Overview
A lightweight, robust news aggregation and cross-publishing subsystem integrated directly into the existing Laravel news application.

## Implemented Components
1. **Database Schema & Models**:
   - `SourceChannel`: Manages source channels (Telegram, Bale, Rubika, Eitaa, RSS) with polling intervals, clean-up rules, and default categories/authors.
   - `DestinationChannel`: Manages cross-publishing destinations with encrypted bot tokens and custom header/footer templates.
   - `AggregatedMessage`: Tracks ingestion lifecycle (`pending`, `processing`, `published`, `skipped`, `failed`, `duplicate`) with composite uniqueness constraint (`platform`, `source_channel_id`, `external_message_id`).
   - `Post` table extended with `is_automated`, `source_channel_id`, and `original_source_url`.

2. **Core Services**:
   - `ContentCleaner`: Deterministic advertisement filter (ad keywords, unwanted phrases) and source link/mention stripper (`@mentions`, `t.me/...`, etc.).
   - `TelegramSourceConnector`: Fetches channel updates and media files via Telegram Bot API.
   - `TelegramPublisher` & `BalePublisher`: Formats and publishes normalized news articles to social destinations.
   - `NewsAggregatorManager`: Orchestrates the end-to-end polling, cleaning, deduplication, media downloading, article creation, and cross-publishing loop.

3. **Automation & Admin UI**:
   - `PollNewsSourcesCommand` (`aggregator:poll`): Scheduled via Laravel scheduler to run every 15 minutes.
   - Admin panel CRUD controllers and Blade views under `admin.aggregator.sources` and `admin.aggregator.destinations`.
   - Sidebar integration and automated article badges (`[خودکار]`) in admin posts list.

4. **Testing**:
   - Feature tests covering content cleaning, ad detection, and source/destination model persistence (`NewsAggregatorTest`).
