<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')->id ?? null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                Rule::notIn([$categoryId]),
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

    public function messages(): array
    {
        return [
            'name.required' => 'وارد کردن نام دسته‌بندی الزامی است',
            'name.unique' => 'این نام دسته‌بندی قبلاً در سیستم ثبت شده است',
            'parent_id.exists' => 'دسته‌بندی والد انتخاب شده معتبر نیست',
            'parent_id.not_in' => 'یک دسته‌بندی نمی‌تواند والد خودش باشد',
            'status.required' => 'وضعیت دسته‌بندی الزامی است',
            'status.in' => 'وضعیت انتخاب شده معتبر نیست',
            'sort_order.integer' => 'ترتیب نمایش باید عدد باشد',
            'sort_order.min' => 'ترتیب نمایش نمی‌تواند کمتر از ۰ باشد',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('sort_order') || $this->sort_order === null) {
            $this->merge(['sort_order' => 0]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('parent_id') && $this->parent_id) {
                $categoryId = $this->route('category')->id ?? null;

                if ($categoryId) {
                    $category = $this->route('category');
                    $childrenIds = $category->children()->pluck('id')->toArray();

                    if (in_array($this->parent_id, $childrenIds)) {
                        $validator->errors()->add(
                            'parent_id',
                            'نمی‌توانید یک زیردسته را به عنوان والد انتخاب کنید'
                        );
                    }
                }
            }
        });
    }
}
