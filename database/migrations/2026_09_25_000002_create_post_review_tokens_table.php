<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * توکن‌های امن برای لینک «بررسی خبر» که از طریق ربات بله برای مدیر ارسال می‌شود.
     * هش SHA-256 توکن ذخیره می‌شود؛ خود توکن هرگز در دیتابیس نگه‌داری نمی‌شود.
     */
    public function up(): void
    {
        Schema::create('post_review_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')
                ->constrained('posts')
                ->cascadeOnDelete();
            $table->string('token_hash', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->string('consumed_by_ip', 45)->nullable();
            $table->timestamps();

            $table->index(['post_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_review_tokens');
    }
};
