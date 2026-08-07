<?php

namespace App\Http\Requests\Author;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
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
        $postId = $this->route('post')->id ?? null;

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
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
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
}
