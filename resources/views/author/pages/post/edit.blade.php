@extends('author.layout.master')

@section('author-title', 'ویرایش خبر')

@section('author-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mb-4">
            <h4 class="mb-1">ویرایش خبر: {{ Str::limit($post->title, 60) }}</h4>
        </div>

        {{-- نمایش دلیل رد برای نویسنده --}}
        @if ($post->status === 'rejected' && $post->rejection_reason)
            <div class="alert alert-danger d-flex align-items-start mb-4" role="alert">
                <i class="bx bx-error-circle fs-4 me-2"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-1">این خبر رد شده است</h5>
                    <p class="mb-2">
                        <strong>دلیل رد:</strong>
                    </p>
                    <p class="mb-2 fst-normal">{{ $post->rejection_reason }}</p>
                    @if ($post->approver)
                        <small class="text-muted">
                            <i class="bx bx-user-check me-1"></i>
                            بررسی‌کننده: {{ $post->approver->name }}
                            @if ($post->updated_at)
                                • {{ verta($post->updated_at)->format('Y/m/d H:i') }}
                            @endif
                        </small>
                    @endif
                    <hr>
                    <p class="mb-0 small">
                        <i class="bx bx-check-circle me-1"></i>
                        پس از اصلاح، دکمه «بروزرسانی خبر» را بزنید تا خبر دوباره برای بررسی ارسال شود.
                    </p>
                </div>
            </div>
        @elseif ($post->status === 'pending')
            <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                <i class="bx bx-time-five fs-4 me-2"></i>
                <div>
                    این خبر در <strong>انتظار بررسی مدیر</strong> است. تا زمان بررسی، می‌توانید آن را ویرایش کنید.
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('author.posts.update', $post) }}" method="POST" enctype="multipart/form-data">
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

                    <div class="row">
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
                            <i class="bx bx-{{ $post->status === 'rejected' ? 'send' : 'save' }} me-1"></i>
                            {{ $post->status === 'rejected' ? 'اصلاح و ارسال مجدد برای بررسی' : 'بروزرسانی خبر' }}
                        </button>
                        <a href="{{ route('author.posts.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-x me-1"></i>
                            انصراف
                        </a>
                        @if ($post->status === 'rejected')
                            <small class="d-block text-muted mt-2">
                                <i class="bx bx-info-circle me-1"></i>
                                با ذخیره، دلیل رد پاک می‌شود و خبر به وضعیت «در انتظار بررسی» برمی‌گردد.
                            </small>
                        @endif
                    </div>
            </div>
            </form>
        </div>
    </div>
    </div>
@endsection

@push('author-scripts')
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
            filebrowserUploadUrl: '{{ route('editor.upload') }}',
            filebrowserImageUploadUrl: '{{ route('editor.upload') }}',
            filebrowserUploadMethod: 'form'
        });
    </script>
@endpush
