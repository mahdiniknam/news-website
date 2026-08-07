    <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
            <a href="index.html" class="app-brand-link">
                <span class="app-brand-logo demo">
                    <img src="../../assets/img/branding/logo.png" alt="لوگو" width="26" height="26">
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
                <a href="{{ route('author.show.dashboard') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div> داشبورد خبرنگار</div>
                </a>
            </li>
        
            @role('author')
                <li class="menu-item">
                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                        <i class="menu-icon tf-icons bx bx-user"></i>
                        <div>پست ها</div>
                    </a>
                    <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="{{ route('author.posts.index') }}" class="menu-link">
                                <div>لیست</div>
                            </a>
                        </li>
                        <li class="menu-item">
                            <a href="{{ route('author.posts.create') }}" class="menu-link">
                                <div>ایجاد</div>
                            </a>
                        </li>
                    </ul>
                </li>
            @endrole
        </ul>
    </aside>
