<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admins,email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'is_active' => ['nullable', 'boolean'],
            // شناسه بله دیگر از فرم گرفته نمی‌شود؛ اتصال فقط با کد احراز هویت از پنل خود کاربر انجام می‌شود
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:roles,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'وارد کردن نام مدیر الزامی است',
            'name.max' => 'نام مدیر نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد',
            'email.required' => 'وارد کردن ایمیل مدیر الزامی است',
            'email.email' => 'فرمت ایمیل وارد شده صحیح نیست',
            'email.unique' => 'این ایمیل قبلاً در سیستم ثبت شده است',
            'password.required' => 'وارد کردن رمز عبور الزامی است',
            'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد',
            'password.confirmed' => 'تکرار رمز عبور مطابقت ندارد',
            'avatar.image' => 'فایل انتخاب شده باید تصویر باشد',
            'avatar.mimes' => 'فرمت تصویر باید jpeg, png, jpg یا gif باشد',
            'avatar.max' => 'حجم تصویر نباید بیشتر از 10 مگابایت باشد',
            'roles.required' => 'حداقل یک نقش باید انتخاب شود',
            'roles.array' => 'فرمت نقش‌ها صحیح نیست',
            'roles.min' => 'حداقل یک نقش باید انتخاب شود',
            'roles.*.exists' => 'نقش انتخاب شده معتبر نیست',
        ];
    }
}
