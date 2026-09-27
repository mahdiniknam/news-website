<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * آرشیو پیام‌های ارسالی ربات بله (برای جلوگیری از ارسال تکراری و عیب‌یابی).
     */
    public function up(): void
    {
        Schema::create('bot_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_id');
            $table->string('subject', 191)->index();
            $table->unsignedBigInteger('notifiable_id')->nullable();
            $table->string('notifiable_type')->nullable();
            $table->text('payload')->nullable();
            $table->string('status', 20)->default('sent');
            $table->string('error_message')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bot_notifications');
    }
};
