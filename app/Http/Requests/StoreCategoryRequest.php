<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name',
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
            ],
            'status' => [
                'required',
                'in:0,1',
            ],
            'sort_order' => [
                'nullable',
                'integer',
                'min:0',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // پیام‌های name
            'name.required' => 'وارد کردن نام دسته‌بندی الزامی است',
            'name.string' => 'نام دسته‌بندی باید متن باشد',
            'name.max' => 'نام دسته‌بندی نمی‌تواند بیشتر از ۲۵۵ کاراکتر باشد',
            'name.unique' => 'این نام دسته‌بندی قبلاً در سیستم ثبت شده است',

            // پیام‌های parent_id
            'parent_id.exists' => 'دسته‌بندی والد انتخاب شده معتبر نیست',

            // پیام‌های status
            'status.required' => 'وضعیت دسته‌بندی الزامی است',
            'status.in' => 'وضعیت انتخاب شده معتبر نیست',

            // پیام‌های sort_order
            'sort_order.integer' => 'ترتیب نمایش باید عدد باشد',
            'sort_order.min' => 'ترتیب نمایش نمی‌تواند کمتر از ۰ باشد',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'نام دسته‌بندی',
            'parent_id' => 'دسته‌بندی والد',
            'status' => 'وضعیت',
            'sort_order' => 'ترتیب نمایش',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // اگر sort_order وارد نشده باشد، مقدار پیش‌فرض 0 قرار بده
        if (!$this->has('sort_order') || $this->sort_order === null) {
            $this->merge([
                'sort_order' => 0,
            ]);
        }

        // اگر status وارد نشده باشد، مقدار پیش‌فرض 1 (فعال) قرار بده
        if (!$this->has('status')) {
            $this->merge([
                'status' => 1,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request after validation.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // جلوگیری از انتخاب خود به عنوان والد
            if ($this->has('parent_id') && $this->parent_id) {
                // اگر دسته‌بندی در حال ویرایش باشد
                if ($this->route('category')) {
                    $categoryId = $this->route('category')->id;
                    if ($this->parent_id == $categoryId) {
                        $validator->errors()->add(
                            'parent_id',
                            'یک دسته‌بندی نمی‌تواند والد خودش باشد'
                        );
                    }
                }
            }
        });
    }
}
