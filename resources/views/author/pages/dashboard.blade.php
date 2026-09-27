@extends('author.layout.master')

@section('author-title')
    داشبورد نویسنده
@endsection

@section('author-content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- خوش‌آمدگویی -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center flex-wrap gap-3">
                            <div class="avatar avatar-lg">
                                @if ($author->avatar)
                                    <img src="{{ asset('storage/' . $author->avatar) }}" alt="{{ $author->name }}"
                                        class="rounded-circle"
                                        style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <span class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center"
                                        style="width: 60px; height: 60px; font-size: 24px;">
                                        {{ mb_substr($author->name, 0, 1) }}
                                    </span>
                                @endif
                            </div>
                            <div class="me-auto">
                                <h4 class="mb-1">خوش آمدید، {{ $author->name }} 👋</h4>
                                <p class="text-muted mb-0">
                                    خبر جدید بنویسید و نتیجه بررسی را در پنل و ربات بله دنبال کنید.
                                </p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('author.posts.create') }}" class="btn btn-primary">
                                    <i class="bx bx-plus me-1"></i> ثبت خبر جدید
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- هشدار اتصال بله --}}
        @if ($botConfigured && ! $baleLinked)
            <div class="alert alert-info d-flex align-items-start" role="alert">
                <i class="bx bx-info-circle fs-4 me-2"></i>
                <div class="flex-grow-1">
                    <strong>اتصال به ربات بله:</strong>
                    برای اینکه نتیجه تایید یا رد خبرهایتان در ربات بله اطلاع داده شود، حساب خود را متصل کنید.
                    <a href="{{ route('account.bale') }}">گرفتن کد اتصال</a>
                </div>
            </div>
        @endif

        {{-- هشدار خبرهای رد‌شده --}}
        @if ($rejectedPosts > 0)
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="bx bx-error fs-4 me-2"></i>
                <div class="flex-grow-1">
                    <strong>{{ $rejectedPosts }} خبر رد‌شده دارید.</strong>
                    دلیل رد را ببینید، خبر را اصلاح و دوباره ارسال کنید.
                </div>
                <a href="{{ route('author.posts.index') }}" class="btn btn-sm btn-outline-warning">
                    بررسی و اصلاح
                </a>
            </div>
        @endif

        <!-- کارت‌های آماری -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block text-muted fw-semibold mb-2">کل خبرهای من</span>
                                <h2 class="mb-0">{{ $totalPosts }}</h2>
                            </div>
                            <div class="avatar avatar-lg bg-label-primary rounded-3 p-2">
                                <i class="bx bx-news fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block text-muted fw-semibold mb-2">در انتظار بررسی</span>
                                <h2 class="mb-0 text-warning">{{ $pendingPosts }}</h2>
                            </div>
                            <div class="avatar avatar-lg bg-label-warning rounded-3 p-2">
                                <i class="bx bx-time-five fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block text-muted fw-semibold mb-2">رد شده</span>
                                <h2 class="mb-0 text-danger">{{ $rejectedPosts }}</h2>
                            </div>
                            <div class="avatar avatar-lg bg-label-danger rounded-3 p-2">
                                <i class="bx bx-x-circle fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block text-muted fw-semibold mb-2">منتشر شده</span>
                                <h2 class="mb-0 text-success">{{ $publishedPosts }}</h2>
                            </div>
                            <div class="avatar avatar-lg bg-label-success rounded-3 p-2">
                                <i class="bx bx-check-circle fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- آخرین اخبار من -->
            <div class="col-xl-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-time me-2 text-primary"></i>
                            آخرین خبرهای من
                        </h5>
                        <a href="{{ route('author.posts.index') }}" class="btn btn-sm btn-outline-primary">
                            مشاهده همه
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($latestPosts as $post)
                                <a href="{{ route('author.posts.edit', $post) }}"
                                    class="list-group-item list-group-item-action d-flex align-items-center py-3">
                                    @if ($post->featured_image)
                                        <img src="{{ asset('storage/' . $post->featured_image) }}"
                                            alt="{{ $post->title }}" class="rounded-3 me-3"
                                            style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="rounded-3 me-3 bg-label-secondary d-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px;">
                                            <i class="bx bx-image text-muted fs-4"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ Str::limit($post->title, 60) }}</h6>
                                        <div class="d-flex flex-wrap gap-2 small text-muted">
                                            <span>
                                                <i class="bx bx-calendar"></i>
                                                {{ verta($post->created_at)->format('Y/m/d') }}
                                            </span>
                                            <span class="badge bg-{{ ['published' => 'success', 'pending' => 'warning', 'approved' => 'info', 'draft' => 'secondary'][$post->status] ?? 'danger' }}">
                                                {{ $post->status_label }}
                                            </span>
                                            @if ($post->status === 'rejected')
                                                <span class="text-danger">
                                                    <i class="bx bx-info-circle"></i> دلیل رد را ببینید
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <i class="bx bx-chevron-left fs-4 text-muted"></i>
                                </a>
                            @empty
                                <div class="text-center py-5">
                                    <i class="bx bx-news fs-1 text-muted"></i>
                                    <p class="mt-2 text-muted">هنوز هیچ خبری ثبت نکرده‌اید</p>
                                    <a href="{{ route('author.posts.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bx bx-plus me-1"></i> ثبت اولین خبر
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- راهنمای گردش کار + دسترسی سریع -->
            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-info-circle me-2 text-primary"></i>
                            گردش کار خبر
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="d-flex align-items-start mb-3">
                                <span class="badge bg-label-primary rounded-circle p-2 me-3">۱</span>
                                <div>خبر جدید ثبت می‌کنید.</div>
                            </li>
                            <li class="d-flex align-items-start mb-3">
                                <span class="badge bg-label-primary rounded-circle p-2 me-3">۲</span>
                                <div>خبر به مدیر ارسال و در <span class="badge bg-label-warning">انتظار بررسی</span> قرار می‌گیرد.</div>
                            </li>
                            <li class="d-flex align-items-start mb-3">
                                <span class="badge bg-label-primary rounded-circle p-2 me-3">۳</span>
                                <div>نتیجه در ربات بله و همین پنل اعلام می‌شود.</div>
                            </li>
                            <li class="d-flex align-items-start">
                                <span class="badge bg-label-success rounded-circle p-2 me-3">۴</span>
                                <div>
                                    اگر رد شد، دلیل را می‌بینید؛ اصلاح و دوباره ارسال می‌کنید.
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-link-alt me-2 text-primary"></i>
                            دسترسی سریع
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('author.posts.create') }}"
                                    class="btn btn-outline-primary w-100 py-3">
                                    <i class="bx bx-plus-circle d-block fs-4"></i>
                                    <span class="d-block mt-1">ثبت خبر جدید</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('author.posts.index') }}"
                                    class="btn btn-outline-info w-100 py-3">
                                    <i class="bx bx-list-ul d-block fs-4"></i>
                                    <span class="d-block mt-1">لیست خبرها</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('account.bale') }}" class="btn btn-outline-success w-100 py-3">
                                    <i class="bx bx-message-square-dots d-block fs-4"></i>
                                    <span class="d-block mt-1">اتصال ربات بله</span>
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('account.profile') }}"
                                    class="btn btn-outline-secondary w-100 py-3">
                                    <i class="bx bx-user d-block fs-4"></i>
                                    <span class="d-block mt-1">اطلاعات من</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
