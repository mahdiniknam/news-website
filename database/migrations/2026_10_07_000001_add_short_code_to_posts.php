<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('posts', 'short_code')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('short_code', 10)->nullable()->unique()->after('slug');
            });
        }

        $posts = DB::table('posts')->whereNull('short_code')->orWhere('short_code', '')->get(['id']);
        foreach ($posts as $row) {
            $code = $this->generateUniqueCode();
            DB::table('posts')->where('id', $row->id)->update(['short_code' => $code]);
        }

        Schema::table('posts', function (Blueprint $table) {
            try { DB::statement('ALTER TABLE posts MODIFY short_code VARCHAR(10) NOT NULL'); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('short_code');
        });
    }

    private function generateUniqueCode(): string
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        do {
            $code = '';
            for ($i = 0; $i < 6; $i++) $code .= $alphabet[random_int(0, 61)];
            $exists = DB::table('posts')->where('short_code', $code)->exists();
        } while ($exists);
        return $code;
    }
};
