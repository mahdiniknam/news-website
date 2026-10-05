<!DOCTYPE html>
<html lang="fa" class="light-style" dir="rtl" data-theme="theme-default" data-assets-path="{{ asset('assets') }}/">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لینک منقضی شده | دیدبان شهر</title>
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
                        <div class="avatar avatar-lg bg-label-danger mb-3">
                            <i class="bx bx-link-alt bx-tada fs-2"></i>
                        </div>
                        <h4 class="mb-2">این لینک قابل استفاده نیست</h4>
                        <p class="text-muted mb-4">
                            لینک بررسی خبر منقضی شده یا قبلاً استفاده شده است.
                            <br>
                            اگر خبر هنوز در انتظار بررسی است، از پنل مدیریت اقدام کنید.
                        </p>
                        <a href="{{ route('home') }}" class="btn btn-primary">
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
