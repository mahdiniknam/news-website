 <header>
     <nav class="navbar navbar-expand-lg navbar-custom">
         <div class="container">
             <div class="logo-container">
                 <a class="logo-text" href="/">
                     <span class="app-brand-logo demo">
                         <img src="{{ asset('assets/img/branding/logo.png') }}" alt="لوگو" width="40" height="40">
                     </span>
                     {{ config('app.name', 'پایگاه خبری دید بان شهر') }}
                 </a>
             </div>

             <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                 <span class="navbar-toggler-icon"></span>
             </button>

             <div class="collapse navbar-collapse" id="navbarNav">
                 <ul class="navbar-nav mx-auto">
                     <li class="nav-item"><a class="nav-link nav-link-custom" href="/">صفحه اصلی</a></li>
                     <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('news') }}">اخبار</a></li>
                     <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('notes') }}">یادداشت‌ها</a>
                     </li>
                     <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('interviews') }}">مصاحبه
                             ها</a>
                     </li>
                 </ul>

                 <div class="d-flex align-items-center gap-2">
                     <div class="social-icons d-none d-lg-block">
                         <!-- ایتا -->
                         <a href="https://eitaa.com/didebaneshahr_ir" target="_blank" rel="noopener noreferrer"
                             aria-label="ایتا" title="ایتا">
                             <img src="{{ asset('assets/img/icons/eitaa.png') }}" alt="ایتا" width="30"
                                 height="30">
                         </a>

                         <!-- بله -->
                         <a href="https://ble.ir/didebaneshahr_ir" target="_blank" rel="noopener noreferrer"
                             aria-label="بله" title="بله">
                             <img src="{{ asset('assets/img/icons/bale.png') }}" alt="بله" width="30"
                                 height="30">
                         </a>

                         <!-- روبیکا -->
                         <a href="https://rubika.ir/didebaneshahr_ir" target="_blank" rel="noopener noreferrer"
                             aria-label="روبیکا" title="روبیکا">
                             <img src="{{ asset('assets/img/icons/rubika.png') }}" alt="روبیکا" width="30"
                                 height="30">
                         </a>

                         <!-- تلگرام -->
                         <a href="https://t.me/didebaneshahr_ir" target="_blank" rel="noopener noreferrer"
                             aria-label="تلگرام" title="تلگرام">
                             <img src="{{ asset('assets/img/icons/telegram.png') }}" alt="تلگرام" width="30"
                                 height="30">
                         </a>

                         <!-- اینستاگرام -->
                         <a href="https://www.instagram.com/didebaneshahr_ir" rel="noopener noreferrer"
                             aria-label="اینستاگرام" title="اینستاگرام">
                             <img src="{{ asset('assets/img/icons/instagram.png') }}" alt="اینستاگرام" width="30"
                                 height="30">
                         </a>
                     </div>

                     <button class="theme-toggle" id="themeToggle" aria-label="تغییر تم">
                         <i id="themeIcon" class="fas fa-moon"></i>
                     </button>
                 </div>
             </div>
         </div>
     </nav>
 </header>
