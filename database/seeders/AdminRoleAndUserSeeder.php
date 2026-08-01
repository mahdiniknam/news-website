<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AdminRoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ۱. پاک کردن کش پکیج اسپاتی قبل از سید کردن برای جلوگیری از تداخل
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ۲. تعریف دسترسی‌های (Permissions) مورد نیاز سیستم با گارد admin
        $permissions = [
            'manage-admins',
            'manage-categories',
            'create-posts',
            'edit-posts',
            'delete-posts',
            'approve-posts', // دسترسی تایید مطالب نویسندگان
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // ۳. ساخت نقش‌ها (Roles)

        // نقش سوپر ادمین (Super Admin)
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'admin'
        ]);

        // نقش نویسنده (Author)
        $authorRole = Role::firstOrCreate([
            'name' => 'author',
            'guard_name' => 'admin'
        ]);
        // نویسنده فقط دسترسی ساخت و ویرایش پست‌های خودش را دارد
        $authorRole->syncPermissions(['create-posts', 'edit-posts']);

        // ۴. اختصاص تمامی دسترسی‌ها به نقش سوپر ادمین
        $superAdminRole->syncPermissions(Permission::where('guard_name', 'admin')->get());

        // ۵. ساخت اولین کاربر سوپر ادمین
        $superAdmin = Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'مدیریت کل',
                'password' => 'password', // به صورت خودکار به وسیله کستِ مدل هش می‌شود
            ]
        );

        // ۶. اختصاص نقش سوپر ادمین به ادمین ساخته شده
        $superAdmin->assignRole($superAdminRole);

        $this->command->info('Super Admin created successfully with email: admin@example.com and password: password');
    }
}
