    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
            <a href="{{ route('author.show.dashboard') }}" class="app-brand-link">
                <span class="app-brand-logo demo">
                    <img src="{{ asset('assets/img/branding/logo.png') }}" alt="لوگو" width="26" height="26">
                </span>
                <span class="app-brand-text demo menu-text fw-bold ms-2">دیدبان شهر</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
                <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
            </a>
        </div>

        <div class="menu-divider mt-0"></div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">

            <li class="menu-item {{ request()->routeIs('author.show.dashboard') ? 'active open' : '' }}">
                <a href="{{ route('author.show.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div>داشبورد</div>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('author.posts.*') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-news"></i>
                    <div>خبرهای من</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ request()->routeIs('author.posts.index') ? 'active' : '' }}">
                        <a href="{{ route('author.posts.index') }}" class="menu-link">
                            <div>لیست خبرها</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('author.posts.create') ? 'active' : '' }}">
                        <a href="{{ route('author.posts.create') }}" class="menu-link">
                            <div>ثبت خبر جدید</div>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-item {{ request()->routeIs('account.profile') ? 'active' : '' }}">
                <a href="{{ route('account.profile') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div>اطلاعات من</div>
                </a>
            </li>

            <li class="menu-item {{ request()->routeIs('account.bale') ? 'active' : '' }}">
                <a href="{{ route('account.bale') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-message-square-dots"></i>
                    <div>اتصال ربات بله</div>
                    <span id="sidebar-bale-badge"></span>
                </a>
            </li>
        </ul>

        <div class="mt-auto p-3">
            <div class="card bg-label-primary">
                <div class="card-body text-center py-3">
                    <i class="bx bx-shield-quarter fs-3 text-primary"></i>
                    <p class="mb-0 small text-muted">
                        این پنل مخصوص ثبت و پیگیری خبر است.
                        مدیریت سایت توسط ادمین انجام می‌شود.
                    </p>
                </div>
            </div>
        </div>
    </aside>
