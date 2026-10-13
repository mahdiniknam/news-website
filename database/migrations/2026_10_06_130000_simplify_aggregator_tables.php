<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('source_channels')) {
            if (Schema::hasColumn('source_channels', 'identifier') && ! Schema::hasColumn('source_channels', 'channel_link')) {
                DB::statement('ALTER TABLE source_channels CHANGE `identifier` `channel_link` VARCHAR(255) NOT NULL');
            }
            if (Schema::hasColumn('source_channels', 'configuration')) {
                Schema::table('source_channels', function (Blueprint $table) {
                    $table->dropColumn('configuration');
                });
            }
            if (Schema::hasColumn('source_channels', 'cleanup_rules')) {
                Schema::table('source_channels', function (Blueprint $table) {
                    $table->dropColumn('cleanup_rules');
                });
            }
            if (Schema::hasColumn('source_channels', 'default_category_id')) {
                try { DB::statement('ALTER TABLE source_channels DROP FOREIGN KEY source_channels_default_category_id_foreign'); } catch (\Throwable $e) {}
                Schema::table('source_channels', function (Blueprint $table) {
                    $table->dropColumn('default_category_id');
                });
            }
            if (Schema::hasColumn('source_channels', 'default_author_id')) {
                try { DB::statement('ALTER TABLE source_channels DROP FOREIGN KEY source_channels_default_author_id_foreign'); } catch (\Throwable $e) {}
                Schema::table('source_channels', function (Blueprint $table) {
                    $table->dropColumn('default_author_id');
                });
            }
            if (! Schema::hasColumn('source_channels', 'filter_keywords')) {
                Schema::table('source_channels', function (Blueprint $table) {
                    $table->text('filter_keywords')->nullable()->after('fetch_interval_minutes');
                });
            }
            if (! Schema::hasColumn('source_channels', 'remove_links')) {
                Schema::table('source_channels', function (Blueprint $table) {
                    $table->boolean('remove_links')->default(true)->after('filter_keywords');
                });
            }
            if (! Schema::hasColumn('source_channels', 'remove_tags')) {
                Schema::table('source_channels', function (Blueprint $table) {
                    $table->boolean('remove_tags')->default(true)->after('remove_links');
                });
            }
            try { DB::statement('ALTER TABLE source_channels MODIFY `name` VARCHAR(255) NULL'); } catch (\Throwable $e) {}
            try { DB::statement('ALTER TABLE source_channels MODIFY `platform` VARCHAR(255) NOT NULL DEFAULT "telegram"'); } catch (\Throwable $e) {}
            try { DB::statement('ALTER TABLE source_channels MODIFY `fetch_interval_minutes` INT NOT NULL DEFAULT 10'); } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('destination_channels')) {
            if (Schema::hasColumn('destination_channels', 'identifier') && ! Schema::hasColumn('destination_channels', 'channel_identifier')) {
                DB::statement('ALTER TABLE destination_channels CHANGE `identifier` `channel_identifier` VARCHAR(255) NOT NULL');
            }
            if (Schema::hasColumn('destination_channels', 'configuration')) {
                Schema::table('destination_channels', function (Blueprint $table) {
                    $table->dropColumn('configuration');
                });
            }
            if (Schema::hasColumn('destination_channels', 'template_header')) {
                Schema::table('destination_channels', function (Blueprint $table) {
                    $table->dropColumn('template_header');
                });
            }
            if (Schema::hasColumn('destination_channels', 'template_footer')) {
                Schema::table('destination_channels', function (Blueprint $table) {
                    $table->dropColumn('template_footer');
                });
            }
            try { DB::statement('ALTER TABLE destination_channels MODIFY `platform` VARCHAR(255) NOT NULL DEFAULT "telegram"'); } catch (\Throwable $e) {}
            try { DB::statement('ALTER TABLE destination_channels MODIFY `bot_token` TEXT NOT NULL'); } catch (\Throwable $e) {}
        }

        if (Schema::hasTable('aggregated_messages') && Schema::hasColumn('aggregated_messages', 'error_message')) {
            Schema::table('aggregated_messages', function (Blueprint $table) {
                $table->dropColumn('error_message');
            });
        }
    }

    public function down(): void
    {
    }
};
