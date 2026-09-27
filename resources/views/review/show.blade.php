<!DOCTYPE html>
<html lang="fa" class="light-style" dir="rtl" data-theme="theme-default" data-assets-path="{{ asset('assets') }}/">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بررسی خبر | دید بان شهر</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/rtl.css') }}">
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <div class="container-xxl">
        <div class="row justify-content-center py-5">
            <div class="col-lg-9 col-xl-8">

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/img/branding/logo.png') }}" alt="لوگو" width="34" height="34"
                            class="me-2">
                        <h4 class="mb-0 fw-bold">دید بان شهر</h4>
                    </div>
                    @if ($isLoggedInAdmin)
                        <a href="{{ route('admin.posts.index') }}" class="btn btn-sm btn-label-primary">
                            <i class="bx bx-arrow-back bx-flip-horizontal me-1"></i> پنل مدیریت
                        </a>
                    @endif
                </div>

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="بستن"></button>
                    </div>
                @endif

                <div class="card mb-4">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <h5 class="mb-0">
                            <i class="bx bx-news me-1"></i>
                            بررسی خبر
                        </h5>
                        <span class="badge bg-warning text-dark">
                            <i class="bx bx-time-five me-1"></i>
                            {{ $post->status_label }}
                        </span>
                    </div>

                    <div class="card-body">
                        <h4 class="mb-2">{{ $post->title }}</h4>

                        <div class="d-flex flex-wrap gap-3 text-muted mb-3">
                            <span>
                                <i class="bx bx-user me-1"></i>
                                {{ $post->author?->name ?? 'نامشخص' }}
                            </span>
                            <span>
                                <i class="bx bx-category me-1"></i>
                                {{ $post->category?->name ?? 'بدون دسته‌بندی' }}
                            </span>
                            <span>
                                <i class="bx bx-purchase-tag me-1"></i>
                                {{ $post->type_label }}
                            </span>
                            <span>
                                <i class="bx bx-calendar me-1"></i>
                                {{ verta($post->created_at)->format('Y/m/d H:i') }}
                            </span>
                        </div>

                        @if ($post->featured_image)
                            <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}"
                                class="img-fluid rounded-3 mb-3" style="max-height: 320px;">
                        @endif

                        <div class="p-3 bg-label-secondary rounded-3 post-review-content">
                            {!! \App\Support\HtmlSanitizer::clean($post->content) !!}
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">
                            <i class="bx bx-check-shield me-1"></i>
                            تصمیم شما
                        </h5>

                        <form method="POST"
                            action="{{ route('posts.review.decide', ['token' => $token]) }}"
                            id="reviewDecisionForm">
                            @csrf

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button type="submit" class="btn btn-success flex-grow-1" name="action"
                                    value="approve">
                                    <i class="bx bx-check-circle me-1"></i>
                                    تایید خبر
                                </button>
                                <button type="button" class="btn btn-danger flex-grow-1" id="showRejectBtn">
                                    <i class="bx bx-x-circle me-1"></i>
                                    رد خبر
                                </button>
                            </div>

                            <div id="rejectReasonBox" class="d-none">
                                <label class="form-label fw-bold">
                                    دلیل رد خبر <span class="text-danger">*</span>
                                </label>
                                <textarea name="rejection_reason" id="rejection_reason" rows="3"
                                    class="form-control @error('rejection_reason') is-invalid @enderror"
                                    placeholder="دلیل رد خبر را برای خبرنگار بنویسید..."></textarea>
                                @error('rejection_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">این دلیل در ربات بله برای خبرنگار ارسال می‌شود.</small>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-danger" name="action" value="reject">
                                        <i class="bx bx-send me-1"></i>
                                        ثبت رد و اطلاع‌رسانی به خبرنگار
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script>
        document.getElementById('showRejectBtn')?.addEventListener('click', function() {
            document.getElementById('rejectReasonBox').classList.remove('d-none');
            document.getElementById('rejection_reason').focus();
        });
    </script>
</body>

</html>
