 <header>
     <nav class="navbar navbar-expand-lg navbar-custom">
         <div class="container">
             <div class="logo-container">
                 <a class="logo-text" href="/">
                     <span class="app-brand-logo demo">
                         <img src="../../assets/img/branding/logo.png" alt="لوگو" width="40" height="40">
                     </span>
                     {{ config('app.name', 'پایگاه خبری دیده بان شهر') }}
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
                      <li class="nav-item"><a class="nav-link nav-link-custom" href="{{ route('interviews') }}">مصاحبه ها</a>
                     </li>
                 </ul>

                 <div class="d-flex align-items-center gap-2">
                     <div class="social-icons d-none d-lg-block">
                         <a href="https://eitaa.com/didebaneshahr_ir" target="_blank" rel="noopener noreferrer"
                             aria-label="ایتا" title="ایتا">
                             <img src="{{ asset('assets/img/icons/eitaa.webp') }}" alt="ایتا" width="30"
                                 height="30">
                         </a>
                         <a href="https://rubika.ir/didebaneshahr_ir" target="_blank" rel="noopener noreferrer"
                             aria-label="روبیکا" title="روبیکا">
                             <img src="{{ asset('assets/img/icons/rubika.png') }}" alt="روبیکا" width="30"
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
