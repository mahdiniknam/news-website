<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // دریافت ID کاربر از مسیر (برای Rule::unique)
        $adminId = $this->route('admin')->id ?? null;

        return [
            // نام - الزامی
            'name' => ['required', 'string', 'max:255'],

            // ایمیل - الزامی، منحصر به فرد (به جز خود کاربر)
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('admins', 'email')->ignore($adminId)
            ],

            // رمز عبور - اختیاری، اگر وارد شود باید حداقل ۸ کاراکتر و با تاییدیه مطابقت داشته باشد
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],

            // بیوگرافی - اختیاری
            'bio' => ['nullable', 'string', 'max:1000'],

            // تصویر - اختیاری، باید تصویر باشد
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'], // 10MB

            // وضعیت فعال - اختیاری، باید boolean باشد
            'is_active' => ['nullable', 'boolean'],

            // شناسه بله دیگر از فرم گرفته نمی‌شود؛ اتصال فقط با کد احراز هویت از پنل خود کاربر انجام می‌شود

            // نقش‌ها - الزامی، حداقل یک نقش
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,name'], // بررسی وجود نقش در دیتابیس
        ];
    }

    public function messages(): array
    {
        return [
            // پیام‌های خطای name
            'name.required' => 'وارد کردن نام مدیر الزامی است',
            'name.string' => 'نام مدیر باید متنی باشد',
            'name.max' => 'نام مدیر نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد',

            // پیام‌های خطای email
            'email.required' => 'وارد کردن ایمیل مدیر الزامی است',
            'email.email' => 'فرمت ایمیل وارد شده صحیح نیست',
            'email.unique' => 'این ایمیل قبلاً در سیستم ثبت شده است',
            'email.max' => 'ایمیل نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد',

            // پیام‌های خطای password
            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد',
            'password.confirmed' => 'تکرار رمز عبور مطابقت ندارد',

            // پیام‌های خطای bio
            'bio.string' => 'بیوگرافی باید متنی باشد',
            'bio.max' => 'بیوگرافی نمی‌تواند بیشتر از ۱۰۰۰ کاراکتر باشد',

            // پیام‌های خطای avatar
            'avatar.image' => 'فایل انتخاب شده باید تصویر باشد',
            'avatar.mimes' => 'فرمت تصویر باید jpeg, png, jpg یا gif باشد',
            'avatar.max' => 'حجم تصویر نباید بیشتر از ۱۰ مگابایت باشد',

            // پیام‌های خطای is_active
            'is_active.boolean' => 'وضعیت فعال باید true یا false باشد',

            // پیام‌های خطای roles
            'roles.required' => 'حداقل یک نقش باید انتخاب شود',
            'roles.array' => 'فرمت نقش‌ها صحیح نیست',
            'roles.min' => 'حداقل یک نقش باید انتخاب شود',
            'roles.*.exists' => 'نقش انتخاب شده معتبر نیست',
        ];
    }
}
