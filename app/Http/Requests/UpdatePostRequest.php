<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $postId = $this->route('post')->id ?? null;
        $user = auth()->guard('admin')->user();
        $isAdmin = $user && ($user->hasRole('super-admin') || $user->hasRole('admin'));

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('posts', 'title')->ignore($postId),
            ],
            'category_id' => 'nullable|exists:categories,id',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_featured' => 'nullable|boolean',

            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'type' => 'nullable|in:news,note,interview',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'وارد کردن عنوان خبر الزامی است',
            'title.unique' => 'این عنوان قبلاً در سیستم ثبت شده است',
            'content.required' => 'متن خبر الزامی است',
            'category_id.exists' => 'دسته‌بندی انتخاب شده معتبر نیست',
            'featured_image.image' => 'فایل انتخاب شده باید تصویر باشد',
            'featured_image.max' => 'حجم تصویر نباید بیشتر از ۵ مگابایت باشد',
            'type.in' => 'نوع خبر انتخاب شده معتبر نیست',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->has('is_featured'),
        ]);
    }
}
