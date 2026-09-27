<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * اعتبارسنجی اسکریپت SQL پشتیبان (database/backup-create-bale-tables.sql).
 *
 * این تست عمداً اسکریپت را روی دیتابیس «اجرا نمی‌کند» (DDL در MySQL قابل
 * rollback نیست) و فقط محتوای آن را بررسی می‌کند تا:
 *  ۱) هرگز دستور مخرب (DROP/TRUNCATE/DELETE) واردش نشود
 *  ۲) همه جدول‌های موردنیاز ربات بله را بسازد
 *  ۳) نام مایگریشن‌های ثبت‌شده‌اش دقیقاً با فایل‌های واقعی مایگریشن همگام بماند
 */
class BackupSqlScriptTest extends TestCase
{
    protected string $script;

    protected function setUp(): void
    {
        parent::setUp();

        $path = database_path('backup-create-bale-tables.sql');

        $this->assertFileExists($path, 'اسکریپت SQL پشتیبان باید موجود باشد');

        $this->script = File::get($path);
    }

    public function test_script_has_no_destructive_statements(): void
    {
        $this->assertDoesNotMatchRegularExpression(
            '/\bDROP\s+(TABLE|DATABASE|SCHEMA)\b/i',
            $this->script,
            'اسکریپت پشتیبان نباید هیچ DROP داشته باشد'
        );

        $this->assertDoesNotMatchRegularExpression(
            '/\bTRUNCATE\b/i',
            $this->script,
            'اسکریپت پشتیبان نباید TRUNCATE داشته باشد'
        );

        $this->assertDoesNotMatchRegularExpression(
            '/\bDELETE\s+FROM\b/i',
            $this->script,
            'اسکریپت پشتیبان نباید DELETE داشته باشد'
        );
    }

    public function test_script_creates_all_required_tables(): void
    {
        foreach (['bale_link_codes', 'post_review_tokens', 'bot_notifications', 'settings'] as $table) {
            $this->assertMatchesRegularExpression(
                '/CREATE\s+TABLE\s+IF\s+NOT\s+EXISTS\s+`?' . preg_quote($table, '/') . '`?/i',
                $this->script,
                "جدول {$table} باید با CREATE TABLE IF NOT EXISTS ساخته شود"
            );
        }
    }

    public function test_script_adds_bale_chat_id_column_safely(): void
    {
        // ستون باید با بررسی INFORMATION_SCHEMA و PREPARE اضافه شود (idempotent)
        $this->assertStringContainsString('INFORMATION_SCHEMA.COLUMNS', $this->script);
        $this->assertStringContainsString('bale_chat_id', $this->script);
        $this->assertStringContainsString('PREPARE stmt', $this->script);
    }

    public function test_script_registers_all_bale_migrations(): void
    {
        // مایگریشن‌های ربات بله را از پوشه واقعی migrations پیدا کن
        $baleMigrations = collect(File::glob(database_path('migrations/*.php')))
            ->map(fn ($file) => basename($file, '.php'))
            ->filter(fn ($name) => str_contains($name, 'bale')
                || str_contains($name, 'settings')
                || str_contains($name, 'post_review_tokens')
                || str_contains($name, 'bot_notifications'))
            ->values();

        $this->assertNotEmpty($baleMigrations, 'باید مایگریشن‌های ربات بله موجود باشند');

        foreach ($baleMigrations as $migration) {
            $this->assertStringContainsString(
                $migration,
                $this->script,
                "مایگریشن {$migration} باید در جدول migrations ثبت شود"
            );
        }
    }

    public function test_script_uses_idempotent_insert_for_migrations(): void
    {
        $this->assertMatchesRegularExpression(
            '/INSERT\s+IGNORE\s+INTO\s+`?migrations`?/i',
            $this->script,
            'ثبت مایگریشن‌ها باید INSERT IGNORE باشد تا اجرای تکراری خطا ندهد'
        );
    }

    public function test_script_ends_with_verification_query(): void
    {
        // بخش آخر باید وضعیت را گزارش کند تا کاربر نتیجه را ببیند
        $this->assertStringContainsString('INFORMATION_SCHEMA.TABLES', $this->script);
    }
}
