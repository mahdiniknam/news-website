<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'is_automated')) {
                $table->boolean('is_automated')->default(false)->after('type');
            }
            if (! Schema::hasColumn('posts', 'source_channel_id')) {
                $table->foreignId('source_channel_id')->nullable()->after('is_automated')->constrained('source_channels')->nullOnDelete();
            }
            if (! Schema::hasColumn('posts', 'original_source_url')) {
                $table->string('original_source_url')->nullable()->after('source_channel_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'source_channel_id')) {
                try { $table->dropForeign(['source_channel_id']); } catch (\Throwable $e) {}
                $table->dropColumn('source_channel_id');
            }
            if (Schema::hasColumn('posts', 'original_source_url')) {
                $table->dropColumn('original_source_url');
            }
            if (Schema::hasColumn('posts', 'is_automated')) {
                $table->dropColumn('is_automated');
            }
        });
    }
};
