<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AddNewPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // پاک کردن کش
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // دسترسی‌های جدید
        $newPermissions = [
            'manage-posts',
            'manage-notes',
            'manage-interviews',
            'publish-posts',
            'reject-posts',
            'view-reports',
            'manage-users',
            'manage-settings',
        ];

        // ایجاد دسترسی‌های جدید
        foreach ($newPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // اختصاص دسترسی‌های جدید به سوپر ادمین
        $superAdminRole = Role::where('name', 'super-admin')
            ->where('guard_name', 'admin')
            ->first();

        if ($superAdminRole) {
            // دادن همه دسترسی‌های جدید به سوپر ادمین
            $superAdminRole->givePermissionTo($newPermissions);

            $this->command->info('دسترسی‌های جدید به سوپر ادمین اختصاص داده شد.');
        }

        // اختصاص به ادمین (اگر نیاز است)
        $adminRole = Role::where('name', 'admin')
            ->where('guard_name', 'admin')
            ->first();

        if ($adminRole) {
            // دسترسی‌هایی که به ادمین می‌دهیم
            $adminPermissions = [
                'manage-posts',
                'manage-notes',
                'manage-interviews',
                'publish-posts',
                'reject-posts',
                'view-reports',
            ];

            $adminRole->givePermissionTo($adminPermissions);
            $this->command->info('دسترسی‌های جدید به ادمین اختصاص داده شد.');
        }

        // پاک کردن کش مجدد
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('دسترسی‌های جدید با موفقیت اضافه شدند!');
    }
}
