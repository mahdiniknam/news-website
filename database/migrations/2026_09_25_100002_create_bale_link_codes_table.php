<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * کدهای اتصال یک‌بارمصرف: کاربر در پنل کد می‌گیرد، آن را در ربات بله
     * ارسال می‌کند و ربات از طریق webhook شناسه گفتگوی او را ذخیره می‌کند.
     */
    public function up(): void
    {
        Schema::create('bale_link_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')
                ->constrained('admins')
                ->cascadeOnDelete();
            $table->string('code', 32)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->unsignedBigInteger('linked_chat_id')->nullable();
            $table->timestamps();

            $table->index(['code', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bale_link_codes');
    }
};
