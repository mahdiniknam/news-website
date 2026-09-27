-- ============================================================================
--  راه پشتیبان: ساخت دستی جدول‌های ربات بله از طریق phpMyAdmin
-- ============================================================================
--  ⚠️ فقط وقتی از این فایل استفاده کن که نتوانی migrate-once.php یا artisan را اجرا کنی.
--
--  ✅ امن است: هیچ جدولی حذف نمی‌شود و به دیتای موجود چیزی اضافه/کم نمی‌شود.
--     - ۴ جدول جدید ساخته می‌شود (اگر از قبل نباشند)
--     - یک ستون nullable به admins اضافه می‌شود (اگر از قبل نباشد)
--     - ثبت در جدول migrations تا بعداً artisan migrate دوباره سعی‌شان نکند
--
--  نحوه استفاده: phpMyAdmin → دیتابیس sirvan_news → تب SQL → کل این فایل را paste کن → Go
--  (می‌توانی چند بار اجرایش کنی؛ دوباره اجرا شدنش خطا نمی‌دهد)
-- ============================================================================

SET NAMES utf8mb4;

-- ----------------------------------------------------------------------------
-- ۱) ستون bale_chat_id روی جدول admins (بدون خطا اگر از قبل exists باشد)
-- ----------------------------------------------------------------------------
SET @col_exists = (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME   = 'admins'
    AND COLUMN_NAME  = 'bale_chat_id'
);

SET @ddl = IF(@col_exists = 0,
  'ALTER TABLE `admins` ADD COLUMN `bale_chat_id` VARCHAR(64) NULL AFTER `is_active`, ADD INDEX `admins_bale_chat_id_index` (`bale_chat_id`)',
  'SELECT 1 AS column_already_exists'
);
PREPARE stmt FROM @ddl; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ----------------------------------------------------------------------------
-- ۲) جدول کدهای اتصال یک‌بارمصرف ربات بله
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bale_link_codes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `admin_id` BIGINT UNSIGNED NOT NULL,
  `code` VARCHAR(32) NOT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `used_at` TIMESTAMP NULL DEFAULT NULL,
  `linked_chat_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bale_link_codes_code_unique` (`code`),
  KEY `bale_link_codes_code_expires_at_index` (`code`, `expires_at`),
  CONSTRAINT `bale_link_codes_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- ۳) جدول توکن‌های امن لینک بررسی خبر (از طریق ربات بله)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `post_review_tokens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` BIGINT UNSIGNED NOT NULL,
  `token_hash` VARCHAR(64) NOT NULL,
  `expires_at` TIMESTAMP NOT NULL,
  `consumed_at` TIMESTAMP NULL DEFAULT NULL,
  `consumed_by_ip` VARCHAR(45) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_review_tokens_token_hash_unique` (`token_hash`),
  KEY `post_review_tokens_post_id_expires_at_index` (`post_id`, `expires_at`),
  CONSTRAINT `post_review_tokens_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- ۴) جدول آرشیو پیام‌های ربات بله
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `bot_notifications` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `chat_id` BIGINT UNSIGNED NOT NULL,
  `subject` VARCHAR(191) NOT NULL,
  `notifiable_id` BIGINT UNSIGNED NULL DEFAULT NULL,
  `notifiable_type` VARCHAR(255) NULL DEFAULT NULL,
  `payload` TEXT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'sent',
  `error_message` VARCHAR(255) NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bot_notifications_subject_index` (`subject`),
  KEY `bot_notifications_notifiable_type_notifiable_id_index` (`notifiable_type`, `notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- ۵) جدول تنظیمات (توکن ربات بله به‌صورت رمزنگاری‌شده)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `key` VARCHAR(100) NOT NULL,
  `value` TEXT NULL,
  `is_encrypted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- ۶) ثبت مایگریشن‌ها در جدول migrations
--    تا اگر بعداً `php artisan migrate` اجرا شد، سعی نکند دوباره بسازد.
--    (batch با بزرگ‌ترین batch موجود تنظیم می‌شود، مثل یک migrate واقعی)
-- ----------------------------------------------------------------------------
SET @mig_batch = (SELECT COALESCE(MAX(`batch`), 0) FROM `migrations`);

INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
  ('2026_09_25_000001_add_bale_chat_id_to_admins_table', @mig_batch),
  ('2026_09_25_000002_create_post_review_tokens_table', @mig_batch),
  ('2026_09_25_000003_create_bot_notifications_table', @mig_batch),
  ('2026_09_25_100001_create_settings_table', @mig_batch),
  ('2026_09_25_100002_create_bale_link_codes_table', @mig_batch);

-- ----------------------------------------------------------------------------
-- پایان — بررسی نهایی: هر ۵ خط زیر باید ببینید
-- ----------------------------------------------------------------------------
SELECT 'bale_chat_id column'  AS item, COUNT(*) AS ok FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='admins' AND COLUMN_NAME='bale_chat_id'
UNION ALL
SELECT 'bale_link_codes table', COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='bale_link_codes'
UNION ALL
SELECT 'post_review_tokens',    COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='post_review_tokens'
UNION ALL
SELECT 'bot_notifications',     COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='bot_notifications'
UNION ALL
SELECT 'settings',              COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='settings';
