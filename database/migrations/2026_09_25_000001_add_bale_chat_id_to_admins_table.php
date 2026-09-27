<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * افزودن شناسه گفتگوی بله به جدول ادمین‌ها (خبرنگارها و مدیران).
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->string('bale_chat_id', 64)
                ->nullable()
                ->after('is_active')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropIndex(['bale_chat_id']);
            $table->dropColumn('bale_chat_id');
        });
    }
};
