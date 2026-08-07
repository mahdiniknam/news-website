@extends('admin.layout.master')

@section('title', 'ویرایش خبر')

@section('admin-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mb-4">
            <h4 class="mb-1">ویرایش خبر: {{ $post->title }}</h4>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- عنوان -->
                        <div class="col-md-8 mb-3">
                            <label class="form-label">عنوان خبر <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $post->title) }}" placeholder="عنوان خبر را وارد کنید">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- وضعیت -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">وضعیت <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                @foreach (\App\Models\Post::statuses() as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('status', $post->status) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- نوع -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">نوع <span class="text-danger">*</span></label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror">
                                @foreach (\App\Models\Post::types() as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('type', $post->type) == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- دسته‌بندی -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">دسته‌بندی</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">بدون دسته‌بندی</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id', $post->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- تصویر شاخص -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">تصویر شاخص</label>
                        @if ($post->featured_image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                                    style="max-height: 100px; border-radius: 5px;">
                            </div>
                        @endif
                        <input type="file" name="featured_image"
                            class="form-control @error('featured_image') is-invalid @enderror">
                        @error('featured_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">فرمت‌های مجاز: jpeg, png, jpg, gif, webp (حداکثر ۵ مگابایت)</small>
                    </div>

                    <!-- محتوا -->
                    <div class="col-12 mb-3">
                        <label class="form-label">محتوا <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" rows="15" class="form-control @error('content') is-invalid @enderror"
                            placeholder="متن خبر را وارد کنید">{{ old('content', $post->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- چک‌باکس‌ها -->
                    <div class="col-md-4 mb-3">
                        <div class="mt-4">
                            <div class="form-check form-switch mb-2">
                                <input type="hidden" name="is_featured" value="0">
                                <input class="form-check-input" type="checkbox" name="is_featured" value="1"
                                    id="is_featured" {{ old('is_featured', $post->is_featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_featured">خبر ویژه</label>
                            </div>

                            <div class="form-check form-switch">
                                <input type="hidden" name="special" value="0">
                                <input class="form-check-input" type="checkbox" name="special" value="1" id="special"
                                    {{ old('special', $post->special) ? 'checked' : '' }}>
                                <label class="form-check-label" for="special">خبر ویژه اسلایدی</label>
                            </div>
                        </div>
                    </div>

                    <!-- متا تگ‌ها -->
                    <div class="col-12">
                        <h5 class="mt-3">متا تگ‌ها (SEO)</h5>
                        <hr>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">عنوان متا</label>
                        <input type="text" name="meta_title"
                            class="form-control @error('meta_title') is-invalid @enderror"
                            value="{{ old('meta_title', $post->meta_title) }}" placeholder="عنوان متا (SEO)">
                        @error('meta_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">توضیحات متا</label>
                        <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" rows="2"
                            placeholder="توضیحات متا (SEO)">{{ old('meta_description', $post->meta_description) }}</textarea>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- دکمه‌ها -->
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i>
                            بروزرسانی خبر
                        </button>
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-x me-1"></i>
                            انصراف
                        </a>
                    </div>
            </div>
            </form>
        </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.ckeditor.com/4.25.2-lts/full/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('content', {
            language: 'fa',
            contentsLangDirection: 'rtl',
            height: 500,
            toolbar: [{
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline', 'Strike']
                },
                {
                    name: 'paragraph',
                    items: ['NumberedList', 'BulletedList', 'Blockquote']
                },
                {
                    name: 'links',
                    items: ['Link', 'Unlink']
                },
                {
                    name: 'insert',
                    items: ['Image', 'Table']
                },
                {
                    name: 'styles',
                    items: ['Format', 'Font', 'FontSize']
                },
                {
                    name: 'colors',
                    items: ['TextColor', 'BGColor']
                },
                {
                    name: 'tools',
                    items: ['Maximize', 'Source']
                }
            ],
            font_names: 'IRANSans;IRAN Sans;Arial;Times New Roman;Verdana;Tahoma;',

            // تنظیمات آپلود تصویر
            filebrowserUploadUrl: '/admin/upload-image',
            filebrowserImageUploadUrl: '/admin/upload-image',
            filebrowserUploadMethod: 'form'
        });
    </script>
@endpush
