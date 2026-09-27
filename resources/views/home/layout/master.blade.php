<!DOCTYPE html>
<html lang="fa" dir="rtl" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'پایگاه خبری دید بان شهر')</title>

    <!-- Bootstrap 5 RTL -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- فونت‌های فارسی -->
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0a58ca;
            --secondary-color: #6c757d;
            --dark-bg: #1a1a2e;
            --dark-surface: #16213e;
            --light-text: #e0e0e0;
        }

        * {
            font-family: 'Vazirmatn', Tahoma, Arial, sans-serif;
        }

        body {
            background-color: var(--bs-body-bg);
            transition: background-color 0.3s, color 0.3s;
        }

        /* هدر */
        .navbar-custom {
            background-color: var(--bs-navbar-bg);
            border-bottom: 2px solid var(--bs-border-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 0.8rem 0;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--bs-heading-color);
            text-decoration: none;
            letter-spacing: -1px;
        }

        .nav-link-custom {
            color: var(--bs-nav-link-color) !important;
            font-weight: 500;
            margin: 0 10px;
            position: relative;
            transition: color 0.3s;
        }

        .nav-link-custom:hover {
            color: var(--bs-primary) !important;
        }

        .nav-link-custom::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            right: 0;
            background-color: var(--bs-primary);
            transition: width 0.3s;
        }

        .nav-link-custom:hover::after {
            width: 100%;
        }

        .social-icons a {
            color: var(--bs-secondary-color);
            margin: 0 5px;
            font-size: 1.2rem;
            transition: color 0.3s, transform 0.3s;
        }

        .social-icons a:hover {
            color: var(--bs-primary);
            transform: scale(1.2);
        }

        /* دکمه تغییر تم */
        .theme-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--bs-body-color);
            cursor: pointer;
            transition: transform 0.3s;
        }

        .theme-toggle:hover {
            transform: rotate(20deg);
        }

        /* اسلایدر */
        .hero-slider {
            margin: 20px 0 30px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .carousel-item {
            height: 400px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .carousel-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.1) 100%);
        }

        .carousel-caption {
            bottom: 30px;
            right: 30px;
            left: 30px;
            text-align: right;
            background: rgba(0, 0, 0, 0.4);
            padding: 20px;
            border-radius: 10px;
            backdrop-filter: blur(5px);
        }

        .carousel-caption h3 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .carousel-caption p {
            font-size: 1rem;
            opacity: 0.9;
        }

        /* کارت‌های خبری */
        .news-card {
            background: var(--bs-card-bg);
            border-radius: 12px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
            border: 1px solid var(--bs-border-color);
        }

        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .news-card-img {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .news-card-body {
            padding: 1.25rem;
        }

        .news-card-title {
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }

        .news-card-title a {
            color: var(--bs-heading-color);
            text-decoration: none;
            transition: color 0.3s;
        }

        .news-card-title a:hover {
            color: var(--bs-primary);
        }

        .news-card-meta {
            font-size: 0.85rem;
            color: var(--bs-secondary-color);
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 10px;
        }

        .news-card-meta i {
            margin-left: 5px;
        }

        .news-card-excerpt {
            color: var(--bs-secondary-color);
            font-size: 0.95rem;
            line-height: 1.8;
            margin-top: 10px;
        }

        .btn-outline-primary-custom {
            color: var(--bs-primary);
            border-color: var(--bs-primary);
            border-radius: 50px;
            padding: 0.4rem 1.5rem;
            transition: all 0.3s;
            font-weight: 500;
        }

        .btn-outline-primary-custom:hover {
            background: var(--bs-primary);
            color: #fff;
        }

        /* بخش یادداشت‌ها (سایدبار) */
        .notes-sidebar {
            background: var(--bs-card-bg);
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--bs-border-color);
        }

        .notes-sidebar-title {
            font-size: 1.2rem;
            font-weight: 700;
            border-bottom: 2px solid var(--bs-primary);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .note-item {
            padding: 12px 0;
            border-bottom: 1px solid var(--bs-border-color);
            transition: transform 0.2s;
        }

        .note-item:hover {
            transform: translateX(5px);
        }

        .note-item:last-child {
            border-bottom: none;
        }

        .note-item-title {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .note-item-title a {
            color: var(--bs-heading-color);
            text-decoration: none;
        }

        .note-item-meta {
            font-size: 0.8rem;
            color: var(--bs-secondary-color);
        }

        /* بخش مصاحبه‌ها */
        .interview-section {
            background: var(--bs-card-bg);
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            border: 1px solid var(--bs-border-color);
        }

        .interview-card {
            display: flex;
            gap: 20px;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid var(--bs-border-color);
            transition: transform 0.2s;
        }

        .interview-card:hover {
            transform: translateX(5px);
        }

        .interview-card:last-child {
            border-bottom: none;
        }

        .interview-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--bs-primary);
            flex-shrink: 0;
        }

        .interview-content {
            flex: 1;
        }

        .interview-title {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .interview-title a {
            color: var(--bs-heading-color);
            text-decoration: none;
        }

        .interview-excerpt {
            color: var(--bs-secondary-color);
            font-size: 0.9rem;
            line-height: 1.8;
        }

        /* فوتر */
        .footer-custom {
            background: var(--bs-dark);
            color: #fff;
            padding: 40px 0 20px;
            margin-top: 40px;
            border-top: 3px solid var(--bs-primary);
        }

        .footer-custom a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-custom a:hover {
            color: #fff;
        }

        .footer-custom .social-icons a {
            font-size: 1.5rem;
            margin: 0 10px;
            color: rgba(255, 255, 255, 0.6);
        }

        .footer-custom .social-icons a:hover {
            color: #fff;
        }

        .footer-custom .footer-links li {
            list-style: none;
            margin-bottom: 10px;
        }

        .footer-custom .footer-links li::before {
            content: '›';
            margin-left: 8px;
            color: var(--bs-primary);
        }

        .footer-copyright {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 20px;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        /* حالت شب */
        [data-bs-theme="dark"] {
            --bs-body-bg: #1a1a2e;
            --bs-card-bg: #16213e;
            --bs-navbar-bg: #0f0f23;
            --bs-heading-color: #e0e0e0;
            --bs-body-color: #d0d0d0;
            --bs-border-color: #2a2a4a;
            --bs-nav-link-color: #d0d0d0;
            --bs-secondary-color: #a0a0a0;
        }

        [data-bs-theme="dark"] .carousel-item::before {
            background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.3) 100%);
        }

        [data-bs-theme="dark"] .footer-custom {
            background: #0a0a1a;
        }

        /* ریسپانسیو */
        @media (max-width: 768px) {
            .carousel-item {
                height: 250px;
            }

            .carousel-caption h3 {
                font-size: 1.2rem;
            }

            .carousel-caption p {
                font-size: 0.8rem;
            }

            .logo-text {
                font-size: 1.2rem;
            }

            .interview-card {
                flex-direction: column;
                text-align: center;
            }

            .notes-sidebar {
                margin-top: 20px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <!-- هدر -->

    @include('home.partial.navbar')
    <!-- محتوای اصلی -->
    <main class="container py-3">
        @yield('content')
    </main>

    <!-- فوتر -->
    <footer class="footer-custom">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3"> {{ config('app.name') }}
                    </h5>
                    <p style="color: rgba(255,255,255,0.7); line-height: 1.8;">
                        امروز مؤثرترین سلاح بین‌‌المللی علیه دشمنان و مخالفین، سلاح تبلیغات است؛ سلاح ارتباطات رسانه‌‌ای
                        است. امروز این قویترینِ سلاح است و از بمب اتم هم بدتر و خطرناکتر است. این سلاح دشمن را شما در
                        بلواهای بعد از انتخابات ندیدید؟ دشمن با همین سلاح، لحظه به لحظه، قضایای ما را دنبال میکرد و به
                        کسانی که اهل شیطنت بودند، رهنمود میداد. «و انّ الشّیاطین لیوحون الی اولیائهم لیجادلوکم»؛ دائم به
                        اولیاء خودشان ایحاء میکردند.
                    </p>
                </div>

                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">دسترسی سریع</h5>
                    <ul class="footer-links" style="padding: 0;">
                        <li><a href="{{ route('news') }}">اخبار</a></li>
                        <li><a href="{{ route('notes') }}">یادداشت ها</a></li>
                        <li><a href="{{ route('interviews') }}">مصاحبه ها</a></li>

                    </ul>
                </div>

                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">ارتباط ها</h5>
                    <ul class="footer-links" style="padding: 0;">
                        <li><a href="https://shora.mashhad.ir/">شورای شهر</a></li>
                        <li><a href="https://www.mashhad.ir/">مدیریت شهری</a></li>
                        <li><a href="https://www.mashhad.khorasan.ir/">فرمانداری</a></li>
                        <li><a href="https://www.khorasan.ir/">استانداری</a></li>
                    </ul>

                    {{-- نماد اعتماد --}}
                    <div class="trustseal-wrapper mt-4 text-center">
                        <div id="div_eRasanehTrustseal_100871"></div>
                    </div>


                </div>

            </div>

            <div class="footer-copyright">
                <p>کلیه حقوق این وب‌سایت محفوظ است &copy; {{ date('Y') }}</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // حالت شب/روز
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const html = document.documentElement;

        function setTheme(theme) {
            html.setAttribute('data-bs-theme', theme);
            localStorage.setItem('theme', theme);
            themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        }

        // بارگذاری تم ذخیره‌شده
        const savedTheme = localStorage.getItem('theme') || 'light';
        setTheme(savedTheme);

        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-bs-theme');
            setTheme(currentTheme === 'dark' ? 'light' : 'dark');
        });
    </script>

    <script src="https://trustseal.e-rasaneh.ir/trustseal.js"></script>
    <script>
        eRasaneh_Trustseal(100871, true);
    </script>
    @stack('scripts')
</body>

</html>
