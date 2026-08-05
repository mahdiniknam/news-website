<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::all();
        return view('admin.pages.admin.index', compact('admins'));
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'admin')
            ->where('name', '!=', 'super-admin')
            ->get();

        return view('admin.pages.admin.create', compact('roles'));
    }

    public function store(StoreAdminRequest $request)
    {
        // dd($request->all());

        try {
            DB::beginTransaction();

            // دریافت داده‌های تایید شده
            $validatedData = $request->validated();

            // هش کردن رمز عبور
            $validatedData['password'] = Hash::make($validatedData['password']);

            // مدیریت آپلود تصویر
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $validatedData['avatar'] = $avatarPath;
            }

            // مدیریت وضعیت فعال/غیرفعال
            $validatedData['is_active'] = $request->has('is_active');

            $roles = $validatedData['roles'] ?? [];
            unset($validatedData['roles']);

            // ایجاد کاربر جدید
            $admin = Admin::create($validatedData);

            if (!empty($roles)) {
                // متد syncRoles از trait HasRoles می‌آید
                $admin->syncRoles($roles);
            }


            DB::commit();

            return redirect()
                ->route('admin.admins.index')
                ->with('success', 'مدیر جدید با موفقیت ایجاد شد.');
        } catch (\Exception $e) {
            DB::rollBack();

            // لاگ کردن خطا
            Log::error('خطا در ایجاد مدیر جدید: ' . $e->getMessage());

            // حذف تصویر آپلود شده در صورت وجود خطا
            if (isset($avatarPath) && Storage::disk('public')->exists($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'متاسفانه خطایی در ایجاد مدیر رخ داد. لطفاً مجدداً تلاش کنید.');
        }
    }

    public function edit(Admin $admin)
    {
        $roles = Role::where('guard_name', 'admin')
            ->where('name', '!=', 'super-admin')
            ->get();

        return view('admin.pages.admin.edit', compact('admin', 'roles'));
    }

    public function update(UpdateAdminRequest $request, Admin $admin)  // Route Model Binding
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();
            $validatedData['is_active'] = $request->has('is_active');

            // آپدیت رمز عبور فقط در صورت وارد شدن
            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }

            // مدیریت آپلود تصویر
            if ($request->hasFile('avatar')) {
                // حذف تصویر قبلی
                if ($admin->avatar && Storage::disk('public')->exists($admin->avatar)) {
                    Storage::disk('public')->delete($admin->avatar);
                }

                $avatarPath = $request->file('avatar')->store('avatars', 'public');
                $validatedData['avatar'] = $avatarPath;
            }

            // مدیریت نقش‌ها
            $roleNames = $validatedData['roles'] ?? [];
            unset($validatedData['roles']);

            // آپدیت اطلاعات کاربر
            $admin->update($validatedData);

            // به‌روزرسانی نقش‌ها
            if (!empty($roleNames)) {
                $admin->syncRoles($roleNames);
            }

            DB::commit();

            return redirect()
                ->route('admin.admins.index')
                ->with('success', 'مدیر با موفقیت ویرایش شد.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('خطا در ویرایش مدیر: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'متاسفانه خطایی در ویرایش مدیر رخ داد.');
        }
    }
}
