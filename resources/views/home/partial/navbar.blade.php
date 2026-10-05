 <header>
     <nav class="navbar navbar-expand-lg navbar-custom border-bottom shadow-sm">
         <div class="container">
             <div class="logo-container">
                 <a class="logo-text d-flex align-items-center gap-2 text-decoration-none" href="/">
                     <span class="app-brand-logo demo">
                         <img src="{{ asset('assets/img/branding/logo.png') }}" alt="لوگو" width="40" height="40">
                     </span>
                     <span class="fw-bold fs-5 text-body">{{ config('app.name', 'پایگاه خبری دیدبان شهر') }}</span>
                 </a>
             </div>

             <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                 <span class="navbar-toggler-icon"></span>
             </button>

             <div class="collapse navbar-collapse" id="navbarNav">
                 <ul class="navbar-nav mx-auto my-2 my-lg-0">
                     <li class="nav-item"><a class="nav-link nav-link-custom px-3 fw-medium" href="/">صفحه اصلی</a></li>
                     <li class="nav-item"><a class="nav-link nav-link-custom px-3 fw-medium" href="{{ route('news') }}">اخبار</a></li>
                     <li class="nav-item"><a class="nav-link nav-link-custom px-3 fw-medium" href="{{ route('notes') }}">یادداشت‌ها</a></li>
                     <li class="nav-item"><a class="nav-link nav-link-custom px-3 fw-medium" href="{{ route('interviews') }}">مصاحبه‌ها</a></li>
                 </ul>

                 <div class="d-flex align-items-center gap-3">
                     <div class="social-icons d-none d-lg-flex align-items-center gap-2">
                         <!-- ایتا -->
                         <a href="https://eitaa.com/didebaneshahr_ir" target="_blank" rel="noopener noreferrer" aria-label="ایتا" title="ایتا">
                             <img src="{{ asset('assets/img/icons/eitaa.png') }}" alt="ایتا" width="28" height="28" class="rounded-1 transition-transform" style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                         </a>

                         <!-- بله -->
                         <a href="https://ble.ir/didebaneshahr_ir" target="_blank" rel="noopener noreferrer" aria-label="بله" title="بله">
                             <img src="{{ asset('assets/img/icons/bale.png') }}" alt="بله" width="28" height="28" class="rounded-1 transition-transform" style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                         </a>

                         <!-- روبیکا -->
                         <a href="https://rubika.ir/didebaneshahr_ir" target="_blank" rel="noopener noreferrer" aria-label="روبیکا" title="روبیکا">
                             <img src="{{ asset('assets/img/icons/rubika.png') }}" alt="روبیکا" width="28" height="28" class="rounded-1 transition-transform" style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                         </a>

                         <!-- تلگرام -->
                         <a href="https://t.me/didebaneshahr_ir" target="_blank" rel="noopener noreferrer" aria-label="تلگرام" title="تلگرام">
                             <img src="{{ asset('assets/img/icons/telegram.png') }}" alt="تلگرام" width="28" height="28" class="rounded-1 transition-transform" style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                         </a>

                         <!-- اینستاگرام -->
                         <a href="https://www.instagram.com/didebaneshahr_ir" target="_blank" rel="noopener noreferrer" aria-label="اینستاگرام" title="اینستاگرام">
                             <img src="{{ asset('assets/img/icons/instagram.png') }}" alt="اینستاگرام" width="28" height="28" class="rounded-1 transition-transform" style="transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.15)'" onmouseout="this.style.transform='scale(1)'">
                         </a>
                     </div>

                     <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-flex align-items-center gap-2 theme-toggle-btn" id="themeToggle" aria-label="تغییر تم">
                         <i id="themeIcon" class="fas fa-moon"></i>
                         <span class="d-none d-sm-inline small">حالت شب/روز</span>
                     </button>
                 </div>
             </div>
         </div>
     </nav>
 </header>
