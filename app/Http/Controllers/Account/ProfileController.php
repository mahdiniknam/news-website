<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * پروفایل کاربر لاگین‌شده (نویسنده یا ادمین).
 * هر کاربر فقط اطلاعات خودش را می‌بیند و ویرایش می‌کند — بدون دسترسی به بقیه.
 */
class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::guard('admin')->user();

        return view('account.profile', ['user' => $user]);
    }

    public function update(Request $request)
    {
        /** @var Admin $user */
        $user = Auth::guard('admin')->user();

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'bio' => ['nullable', 'string', 'max:1000'],
                'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'current_password' => ['required_with:password', 'current_password:admin'],
            ],
            [
                'name.required' => 'وارد کردن نام الزامی است',
                'avatar.image' => 'فایل انتخاب‌شده باید تصویر باشد',
                'avatar.mimes' => 'فرمت تصویر باید jpeg, png, jpg, gif یا webp باشد',
                'avatar.max' => 'حجم تصویر نباید بیشتر از ۵ مگابایت باشد',
                'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد',
                'password.confirmed' => 'تکرار رمز عبور مطابقت ندارد',
                'current_password.required_with' => 'برای تغییر رمز، رمز فعلی را وارد کنید',
                'current_password.current_password' => 'رمز عبور فعلی صحیح نیست',
            ]
        );

        $user->name = $validated['name'];
        $user->bio = $validated['bio'] ?? null;

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'اطلاعات پروفایل با موفقیت ذخیره شد.');
    }
}
