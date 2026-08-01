<?php

return [

    'models' => [

        /*
         * مدل مربوط به Permission پکیج اسپاتی.
         */
        'permission' => Spatie\Permission\Models\Permission::class,

        /*
         * مدل مربوط به Role پکیج اسپاتی.
         */
        'role' => Spatie\Permission\Models\Role::class,

    ],

    'table_names' => [

        /*
         * نام جدول‌های مربوط به پکیج در دیتابیس.
         */
        'roles' => 'roles',

        'permissions' => 'permissions',

        'model_has_permissions' => 'model_has_permissions',

        'model_has_roles' => 'model_has_roles',

        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        /*
         * فیلد کلید اصلی مدل شما (که در اینجا ادمین‌ها هستند) در جدول واسط پلی‌مورفیک.
         * مقدار پیش‌فرض model_uuid یا model_id است.
         */
        'role_pivot_key' => null, // به صورت پیش‌فرض روی role_id می‌رود
        'permission_pivot_key' => null, // به صورت پیش‌فرض روی permission_id می‌رود
        'model_morph_key' => 'model_id',
    ],

    /*
     * گارد پیش‌فرض سیستم برای اعتبارسنجی نقش‌ها.
     * چون می‌خواهید نقش‌ها را به ادمین‌ها/نویسنده‌ها اختصاص دهید، آن را روی admin می‌گذاریم.
     */
    'default_guard_name' => 'admin',

    /*
     * تنظیمات مربوط به رجیستر کردن و اتصال خودکار قوانین به Gateهای لاراول.
     */
    'register_permission_check_method' => true,

    /*
     * فعال یا غیرفعال کردن سیستم ثبت مجوزهای Gate برای دسترسی سریع‌تر.
     */
    'register_octane_reset_listener' => false,

    /*
     * مشخص می‌کند آیا دسترسی‌ها در سیستم Gate لاراول تعریف شوند یا خیر.
     */
    'teams' => false,

    /*
     * تنظیمات مربوط به کش پکیج برای بهینه‌سازی کوئری‌های دیتابیس.
     */
    'cache' => [

        /*
         * مدت زمان ذخیره کش مجوزها و نقش‌ها (به ثانیه). ۲۴ ساعت.
         */
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        /*
         * کلید ذخیره‌سازی کش.
         */
        'key' => 'spatie.permission.cache',

        /*
         * درایور کش (مثلاً redis یا database یا file).
         * مقدار default از فایل کانفیگ کش اصلی پروژه خوانده می‌شود.
         */
        'store' => 'default',
    ],
];
