<!DOCTYPE html>
<html lang="fa" class="light-style" dir="rtl" data-theme="theme-default" data-assets-path="{{ asset('assets') }}/">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتیجه بررسی | دیدبان شهر</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/core.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/theme-default.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/rtl/rtl.css') }}">
</head>

<body>
    <div class="container-xxl">
        <div class="row justify-content-center py-5">
            <div class="col-md-6 col-lg-5">
                <div class="card text-center">
                    <div class="card-body py-5">
                        @if ($result['approved'])
                            <div class="avatar avatar-lg bg-label-success mb-3">
                                <i class="bx bx-check-circle fs-2"></i>
                            </div>
                            <h4 class="mb-2">خبر تایید شد ✅</h4>
                        @else
                            <div class="avatar avatar-lg bg-label-danger mb-3">
                                <i class="bx bx-x-circle fs-2"></i>
                            </div>
                            <h4 class="mb-2">خبر رد شد ❌</h4>
                        @endif

                        <p class="text-muted mb-1">
                            «{{ $result['title'] }}»
                        </p>
                        <p class="text-muted">
                            پیام نتیجه به‌صورت خودکار برای خبرنگار در ربات بله ارسال شد.
                        </p>

                        <a href="{{ route('home') }}" class="btn btn-primary mt-2">
                            <i class="bx bx-home me-1"></i>
                            صفحه اصلی
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
