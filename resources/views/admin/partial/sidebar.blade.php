    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
            <a href="index.html" class="app-brand-link">
                <span class="app-brand-logo demo">
                    <img src="{{ asset('assets/img/branding/logo.png') }}" alt="لوگو" width="26" height="26">
                </span>
                <span class="app-brand-text demo menu-text fw-bold ms-2">دید بان شهر</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
                <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
            </a>
        </div>

        <div class="menu-divider mt-0"></div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">

            <!-- Apps & Pages -->

            <li class="menu-item">
                <a href="{{ route('admin.show.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div data-i18n="Dashboards">داشبورد</div>
                </a>
            </li>
            @role('super-admin')
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-user"></i>
                        <div data-i18n="Users">کاربران</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{ route('admin.admins.index') }}" class="menu-link">
                                <div data-i18n="List">لیست</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('admin.admins.create') }}" class="menu-link">
                                <div>ایجاد</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endrole
            @role('super-admin')
                <li class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active open' : '' }}">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-check-shield"></i>
                        <div>نقش‌ها و مجوزها</div>
                    </a>

                    <ul class="menu-sub">
                        <li class="menu-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <a href="{{ route('admin.roles.index') }}" class="menu-link">
                                <div>نقش‌ها</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endrole
            @role('super-admin')
                <li class="menu-item">
                    <a href="{{ route('admin.tags.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                        <div>تگ ها</div>
                    </a>
                </li>
            @endrole
            @role('super-admin')
                <li class="menu-item">
                    <a href="{{ route('admin.categories.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-home-circle"></i>
                        <div>دسته بندی ها</div>
                    </a>
                </li>
            @endrole
            @role('super-admin')
                <li class="menu-item {{ request()->routeIs('admin.bale.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.bale.index') }}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-bot"></i>
                        <div>تنظیمات ربات بله</div>
                    </a>
                </li>
            @endrole
            @role('super-admin')
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-user"></i>
                        <div>خبر ها</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{ route('admin.posts.index') }}" class="menu-link">
                                <div>لیست</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('admin.posts.create') }}" class="menu-link">
                                <div>ایجاد</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endrole
        </ul>
    </aside>
