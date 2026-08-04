@extends('admin.layout.master')

@section('admin-title')
    داشبورد مدیریت
@endsection

@section('admin-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <!-- Gamification Card -->
            <div class="col-lg-4 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="card-title mb-2">تبریک می‌گوییم تونی!</h3>
                        <span class="d-block mb-4 text-nowrap primary-font">بهترین فروشنده ماه</span>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-end">
                            <div class="col-6">
                                <h1 class="display-6 text-primary mb-2 pt-3 pb-2">890 تومان</h1>
                                <small class="d-block mb-3 lh-1-85">شما امروز 57% فروش <br>بیشتری
                                    داشتید.</small>

                                <a href="javascript:;" class="btn btn-sm btn-primary">مشاهده
                                    فروش‌ها</a>
                            </div>
                            <div class="col-6 text-end">
                                <img src="../../assets/img/illustrations/prize-light.png" width="140" height="150"
                                    class="rounded-start" alt="View Sales"
                                    data-app-light-img="illustrations/prize-light.png"
                                    data-app-dark-img="illustrations/prize-dark.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Gamification Card -->

            <!-- Multi Radial Chart -->
            <div class="col-lg-4 col-md-6 col-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">بازدید های 1401</h5>
                        <div class="dropdown primary-font">
                            <button class="btn p-0" type="button" id="visitsOptions" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="visitsOptions">
                                <a class="dropdown-item" href="javascript:void(0);">انتخاب همه</a>
                                <a class="dropdown-item" href="javascript:void(0);">تازه سازی</a>
                                <a class="dropdown-item" href="javascript:void(0);">اشتراک گذاری</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="visitsRadialChart"></div>
                    </div>
                </div>
            </div>
            <!--/ Multi Radial Chart -->

            <!-- Statistics cards & Revenue Growth Chart -->
            <div class="col-lg-4 col-12">
                <div class="row">
                    <!-- Statistics Cards -->
                    <div class="col-6 col-md-3 col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="avatar mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle bg-label-success"><i
                                            class="bx bx-purchase-tag fs-4"></i></span>
                                </div>
                                <span class="d-block text-nowrap pt-1">خرید</span>
                                <h2 class="mb-n3">65</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 col-lg-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <div class="avatar mx-auto mb-2">
                                    <span class="avatar-initial rounded-circle bg-label-danger"><i
                                            class="bx bx-cart fs-4"></i></span>
                                </div>
                                <span class="d-block text-nowrap pt-1">سفارش</span>
                                <h2 class="mb-n3">40</h2>
                            </div>
                        </div>
                    </div>
                    <!--/ Statistics Cards -->
                    <!-- Revenue Growth Chart -->
                    <div class="col-12 col-md-6 col-lg-12 mb-4">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center pb-0">
                                <h5 class="card-title mb-0">رشد درآمد</h5>
                                <span class="primary-font">25,980</span>
                            </div>
                            <div class="card-body pb-0">
                                <div id="revenueGrowthChart"></div>
                            </div>
                        </div>
                    </div>
                    <!--/ Revenue Growth Chart -->
                </div>
            </div>
            <!--/ Statistics cards & Revenue Growth Chart -->

            <!-- Weekly Order Summary -->
            <div class="col-xl-8 col-12 mb-4">
                <div class="card h-100">
                    <div class="row row-bordered m-0">
                        <!-- Order Summary -->
                        <div class="col-md-8 col-12 pe-0">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">خلاصه سفارش هفتگی</h5>
                                <div class="dropdown primary-font">
                                    <button class="btn p-0" type="button" id="orderSummaryOptions"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orderSummaryOptions">
                                        <a class="dropdown-item" href="javascript:void(0);">انتخاب
                                            همه</a>
                                        <a class="dropdown-item" href="javascript:void(0);">تازه
                                            سازی</a>
                                        <a class="dropdown-item" href="javascript:void(0);">اشتراک
                                            گذاری</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div id="orderSummaryChart"></div>
                            </div>
                        </div>
                        <!-- Sales History -->
                        <div class="col-md-4 col-12 px-0">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">نمای کلی فروش‌ها</h5>
                                <div class="dropdown primary-font">
                                    <button class="btn p-0" type="button" id="salesOverviewOptions"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="salesOverviewOptions">
                                        <a class="dropdown-item" href="javascript:void(0);">انتخاب
                                            همه</a>
                                        <a class="dropdown-item" href="javascript:void(0);">تازه
                                            سازی</a>
                                        <a class="dropdown-item" href="javascript:void(0);">اشتراک
                                            گذاری</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="mt-1">هفته قبل</h6>
                                <p class="mb-4">کارایی 45% بهتر نسبت به ماه قبل</p>
                                <ul class="list-unstyled m-0 pt-0">
                                    <li class="mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar avatar-sm flex-shrink-0 me-2">
                                                <span class="avatar-initial rounded bg-label-primary"><i
                                                        class="bx bx-trending-up"></i></span>
                                            </div>
                                            <div>
                                                <p class="mb-0 text-muted text-nowrap">درآمد این ماه
                                                </p>
                                                <small class="fw-semibold text-nowrap">84,789
                                                    تومان</small>
                                            </div>
                                        </div>
                                        <div class="progress" style="height: 6px">
                                            <div class="progress-bar bg-primary" style="width: 75%" role="progressbar"
                                                aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="avatar avatar-sm flex-shrink-0 me-2">
                                                <span class="avatar-initial rounded bg-label-success"><i
                                                        class="bx bx-dollar"></i></span>
                                            </div>
                                            <div>
                                                <p class="mb-0 text-muted text-nowrap">میانگین فروش
                                                    روزانه</p>
                                                <small class="fw-semibold text-nowrap">12,398
                                                    تومان</small>
                                            </div>
                                        </div>
                                        <div class="progress" style="height: 6px">
                                            <div class="progress-bar bg-success" style="width: 75%" role="progressbar"
                                                aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Weekly Order Summary -->

            <!-- Latest Update -->
            <div class="col-md-6 col-lg-6 col-xl-4 col-xl-4 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between mb-3">
                        <h5 class="card-title mb-0">آخرین به‌روزرسانی</h5>
                        <div class="dropdown primary-font">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                id="orederStatistics" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                1401
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                                <a class="dropdown-item" href="javascript:void(0);">1401</a>
                                <a class="dropdown-item" href="javascript:void(0);">1400</a>
                                <a class="dropdown-item" href="javascript:void(0);">1399</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="p-0 m-0">
                            <li class="d-flex mb-3 align-items-center">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary"><i
                                            class="bx bx-cube"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <p class="mb-0">مجموع محصولات</p>
                                        <small class="text-muted">2K محصول جدید</small>
                                    </div>
                                    <div class="item-progress">10K</div>
                                </div>
                            </li>
                            <li class="d-flex mb-3 align-items-center">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-info"><i
                                            class="bx bx-pie-chart-alt"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <p class="mb-0">کل فروش</p>
                                        <small class="text-muted">39K فروش جدید</small>
                                    </div>
                                    <div class="item-progress">26M</div>
                                </div>
                            </li>
                            <li class="d-flex mb-3 align-items-center">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-danger"><i
                                            class="bx bx-credit-card"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <p class="mb-0">کل درآمد</p>
                                        <small class="text-muted">43K درآمد جدید</small>
                                    </div>
                                    <div class="item-progress">15M</div>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div class="avatar avatar-sm flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded-circle bg-label-success"><i
                                            class="bx bx-dollar"></i></span>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <p class="mb-0">هزینه کل</p>
                                        <small class="text-muted">مجموع هزینه‌ها</small>
                                    </div>
                                    <div class="item-progress">2B</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--/ Latest Update -->

            <!-- All Users -->
            <div class="col-md-6 col-lg-6 col-xl-4 mb-4 mb-xl-0">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-3">کل کاربران</h5>
                        <h1 class="display-6 fw-normal mb-0 primary-font">8,634,820</h1>
                    </div>
                    <div class="card-body">
                        <span class="d-block mb-2">فعالیت کنونی</span>
                        <div class="progress progress-stacked mb-3 mb-xl-5" style="height: 8px">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 30%"
                                aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 15%" aria-valuenow="15"
                                aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar bg-info" role="progressbar" style="width: 10%" aria-valuenow="10"
                                aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 40%"
                                aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar bg-warning" role="progressbar" style="width: 15%"
                                aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <ul class="p-0 m-0">
                            <li class="mb-3 d-flex justify-content-between">
                                <div class="d-flex align-items-center me-3">
                                    <span class="badge badge-dot bg-success me-2"></span> ایران
                                </div>
                                <div class="d-flex gap-3">
                                    <span>29.5k</span>
                                    <span class="fw-semibold">56%</span>
                                </div>
                            </li>
                            <li class="mb-3 d-flex justify-content-between">
                                <div class="d-flex align-items-center me-3">
                                    <span class="badge badge-dot bg-danger me-2"></span> فرانسه
                                </div>
                                <div class="d-flex gap-3">
                                    <span>25.7k</span>
                                    <span class="fw-semibold">26%</span>
                                </div>
                            </li>
                            <li class="mb-3 d-flex justify-content-between">
                                <div class="d-flex align-items-center me-3">
                                    <span class="badge badge-dot bg-info me-2"></span> ایتالیا
                                </div>
                                <div class="d-flex gap-3">
                                    <span>11.5k</span>
                                    <span class="fw-semibold">34%</span>
                                </div>
                            </li>
                            <li class="mb-3 d-flex justify-content-between">
                                <div class="d-flex align-items-center me-3">
                                    <span class="badge badge-dot bg-primary me-2"></span> چین
                                </div>
                                <div class="d-flex gap-3">
                                    <span>48.5k</span>
                                    <span class="fw-semibold">45%</span>
                                </div>
                            </li>
                            <li class="mb-1 d-flex justify-content-between">
                                <div class="d-flex align-items-center me-3">
                                    <span class="badge badge-dot bg-warning me-2"></span> هندوستان
                                </div>
                                <div class="d-flex gap-3">
                                    <span>22.1k</span>
                                    <span class="fw-semibold">7%</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--/ All Users -->

            <!-- Marketing Campaigns -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">کمپین‌های بازاریابی</h5>
                        <div class="dropdown primary-font">
                            <button class="btn p-0" type="button" id="marketingOptions" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="marketingOptions">
                                <a class="dropdown-item" href="javascript:void(0);">انتخاب همه</a>
                                <a class="dropdown-item" href="javascript:void(0);">تازه سازی</a>
                                <a class="dropdown-item" href="javascript:void(0);">اشتراک گذاری</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex justify-content-between align-content-center flex-wrap gap-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div id="marketingCampaignChart1"></div>
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <h6 class="mb-0 me-2">25,768</h6>
                                            <span class="text-success">(+16.2%)</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-dot bg-success me-2"></span> 12
                                            فروردین 1401
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <div id="marketingCampaignChart2"></div>
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <h6 class="mb-0 me-2">5,352</h6>
                                            <span class="text-danger">(-4.9%)</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-dot bg-danger me-2"></span> 12
                                            فروردین 1401
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="javascript:;" class="btn btn-sm btn-primary">مشاهده گزارش</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table border-top">
                            <thead>
                                <tr>
                                    <th>کمپین</th>
                                    <th>رشد</th>
                                    <th>عوارض</th>
                                    <th>وضعیت</th>
                                    <th>عمل</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <tr>
                                    <td class="text-nowrap">
                                        <img src="../../assets/img/icons/brands/fastrack.png" class="me-3"
                                            width="22" alt="Fastrack">لورم ایپسوم
                                        متن ساختگی
                                    </td>
                                    <td class="text-nowrap"><i class="bx bx-trending-up text-success me-2"></i>28.5%</td>
                                    <td>1,324</td>
                                    <td><span class="text-success">فعال</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn p-0" type="button" id="action1"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action1">
                                                <a class="dropdown-item" href="javascript:void(0);">جزئیات</a>
                                                <a class="dropdown-item" href="javascript:void(0);">نوشتن یک نقد و
                                                    بررسی</a>
                                                <a class="dropdown-item" href="javascript:void(0);">دریافت صورتحساب</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <img src="../../assets/img/icons/brands/puma.png" class="me-3" width="22"
                                            alt="Puma">لورم ایپسوم
                                    </td>
                                    <td class="text-nowrap"><i class="bx bx-trending-down text-danger me-2"></i>56.6%</td>
                                    <td>3,573</td>
                                    <td><span class="text-success">فعال</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn p-0" type="button" id="action2"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action2">
                                                <a class="dropdown-item" href="javascript:void(0);">جزئیات</a>
                                                <a class="dropdown-item" href="javascript:void(0);">نوشتن یک نقد و
                                                    بررسی</a>
                                                <a class="dropdown-item" href="javascript:void(0);">دریافت صورتحساب</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <img src="../../assets/img/icons/brands/nike.png" class="me-3" width="22"
                                            alt="Nike">لورم ایپسوم
                                        متن
                                    </td>
                                    <td class="text-nowrap"><i class="bx bx-trending-up text-success me-2"></i>23.8%</td>
                                    <td>12,347</td>
                                    <td><span class="text-danger">تعطیل</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn p-0" type="button" id="action3"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action3">
                                                <a class="dropdown-item" href="javascript:void(0);">جزئیات</a>
                                                <a class="dropdown-item" href="javascript:void(0);">نوشتن یک نقد و
                                                    بررسی</a>
                                                <a class="dropdown-item" href="javascript:void(0);">دریافت صورتحساب</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <img src="../../assets/img/icons/brands/oneplus.png" class="me-3"
                                            width="22" alt="OnePlus">لورم ایپسوم
                                        متن
                                    </td>
                                    <td class="text-nowrap"><i class="bx bx-trending-up text-success me-2"></i>81.4%</td>
                                    <td>5,347</td>
                                    <td><span class="text-success">فعال</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn p-0" type="button" id="action4"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action4">
                                                <a class="dropdown-item" href="javascript:void(0);">جزئیات</a>
                                                <a class="dropdown-item" href="javascript:void(0);">نوشتن یک نقد و
                                                    بررسی</a>
                                                <a class="dropdown-item" href="javascript:void(0);">دریافت صورتحساب</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <img src="../../assets/img/icons/brands/google.png" class="me-3" width="22"
                                            alt="Google">لورم ایپسوم
                                        متن
                                    </td>
                                    <td class="text-nowrap"><i class="bx bx-trending-down text-danger me-2"></i>12.8%</td>
                                    <td>45,678</td>
                                    <td><span class="text-danger">تعطیل</span></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn p-0" type="button" id="action5"
                                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="action5">
                                                <a class="dropdown-item" href="javascript:void(0);">جزئیات</a>
                                                <a class="dropdown-item" href="javascript:void(0);">نوشتن یک نقد و
                                                    بررسی</a>
                                                <a class="dropdown-item" href="javascript:void(0);">دریافت صورتحساب</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--/ Marketing Campaigns -->
        </div>
    </div>
@endsection
