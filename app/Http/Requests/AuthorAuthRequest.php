<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AuthorAuthRequest extends FormRequest
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
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'وارد کردن ایمیل الزامیست',
            'email.email' => 'ایمیل صحیح وارد کنید',
            'password.required' => 'وارد کردن پسورد الزامی است',
            'password.min' => 'رمز عبور حداقل باید 8 کاراکتر باشد',
        ];
    }
}
