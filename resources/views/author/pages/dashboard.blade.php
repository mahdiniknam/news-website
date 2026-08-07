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
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-lg me-3">
                                @if ($author->avatar)
                                    <img src="{{ asset('storage/' . $author->avatar) }}" alt="{{ $author->name }}"
                                        class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="avatar-initial rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                        style="width: 60px; height: 60px; font-size: 24px;">
                                        {{ substr($author->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="mb-0">خوش آمدید، {{ $author->name }}</h4>
                                <p class="text-muted mb-0">نقش: {{ $author->roles->first()->display_name ?? 'نویسنده' }}</p>
                            </div>
                            <div class="ms-auto">
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="bx bx-check-circle me-1"></i>
                                    آنلاین
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- کارت‌های آماری -->
        <div class="row g-4 mb-4">
            <!-- کل اخبار -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block text-muted fw-semibold mb-2">کل اخبار من</span>
                                <h2 class="mb-0">{{ $totalPosts }}</h2>
                                <small class="text-muted">
                                    <span class="text-success">
                                        <i class="bx bx-up-arrow-alt"></i>
                                        {{ $postsGrowth ?? 0 }}%
                                    </span>
                                    نسبت به ماه قبل
                                </small>
                            </div>
                            <div class="avatar avatar-lg bg-primary bg-opacity-10 rounded-3 p-2">
                                <i class="bx bx-news fs-1 text-primary"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-primary" style="width: {{ $postsPercentage ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- کل یادداشت‌ها -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block text-muted fw-semibold mb-2">کل یادداشت‌های من</span>
                                <h2 class="mb-0">{{ $totalNotes }}</h2>
                                <small class="text-muted">
                                    <span class="text-warning">
                                        <i class="bx bx-up-arrow-alt"></i>
                                        {{ $notesGrowth ?? 0 }}%
                                    </span>
                                    نسبت به ماه قبل
                                </small>
                            </div>
                            <div class="avatar avatar-lg bg-warning bg-opacity-10 rounded-3 p-2">
                                <i class="bx bx-pencil fs-1 text-warning"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: {{ $notesPercentage ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- کل مصاحبه‌ها -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block text-muted fw-semibold mb-2">کل مصاحبه‌های من</span>
                                <h2 class="mb-0">{{ $totalInterviews }}</h2>
                                <small class="text-muted">
                                    <span class="text-danger">
                                        <i class="bx bx-up-arrow-alt"></i>
                                        {{ $interviewsGrowth ?? 0 }}%
                                    </span>
                                    نسبت به ماه قبل
                                </small>
                            </div>
                            <div class="avatar avatar-lg bg-danger bg-opacity-10 rounded-3 p-2">
                                <i class="bx bx-microphone fs-1 text-danger"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger" style="width: {{ $interviewsPercentage ?? 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- وضعیت کلی -->
            <div class="col-xl-3 col-lg-6 col-md-6 col-12">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block text-muted fw-semibold mb-2">وضعیت کلی</span>
                                <h2 class="mb-0">{{ $publishedPosts }}</h2>
                                <small class="text-muted">
                                    <span class="text-success">
                                        <i class="bx bx-check-circle"></i>
                                        منتشر شده
                                    </span>
                                </small>
                            </div>
                            <div class="avatar avatar-lg bg-success bg-opacity-10 rounded-3 p-2">
                                <i class="bx bx-check-shield fs-1 text-success"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success"
                                    style="width: {{ $totalPosts > 0 ? round(($publishedPosts / $totalPosts) * 100) : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- وضعیت اخبار من -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-stats me-2 text-primary"></i>
                            وضعیت اخبار من
                        </h5>
                        <span class="badge bg-primary">{{ $totalPosts }} کل اخبار</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3 col-6">
                                <div class="text-center p-3 bg-light rounded-3">
                                    <span class="d-block text-muted small">منتشر شده</span>
                                    <h4 class="text-success mb-0">{{ $publishedPosts }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center p-3 bg-light rounded-3">
                                    <span class="d-block text-muted small">در انتظار تایید</span>
                                    <h4 class="text-warning mb-0">{{ $pendingPosts }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center p-3 bg-light rounded-3">
                                    <span class="d-block text-muted small">پیش‌نویس</span>
                                    <h4 class="text-secondary mb-0">{{ $draftPosts }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center p-3 bg-light rounded-3">
                                    <span class="d-block text-muted small">رد شده</span>
                                    <h4 class="text-danger mb-0">{{ $rejectedPosts }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- آخرین اخبار و آمار امروز -->
        <div class="row g-4">
            <!-- آخرین اخبار من -->
            <div class="col-xl-6 col-12">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-time me-2 text-primary"></i>
                            آخرین اخبار من
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
                                        <div class="rounded-3 me-3 bg-light d-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px;">
                                            <i class="bx bx-image text-muted fs-4"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $post->title }}</h6>
                                        <div class="d-flex flex-wrap gap-3 small text-muted">
                                            <span>
                                                <i class="bx bx-calendar"></i>
                                                {{ verta($post->created_at)->format('Y/m/d') }}
                                            </span>
                                            <span
                                                class="badge {{ $post->status === 'published' ? 'bg-success' : ($post->status === 'pending' ? 'bg-warning' : ($post->status === 'draft' ? 'bg-secondary' : 'bg-danger')) }}">
                                                {{ $post->status_label }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-5">
                                    <i class="bx bx-news fs-1 text-muted"></i>
                                    <p class="mt-2 text-muted">هنوز هیچ خبری ایجاد نکرده‌اید</p>
                                    <a href="{{ route('author.posts.create') }}" class="btn btn-primary btn-sm">
                                        ایجاد خبر جدید
                                    </a>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- آمار امروز و دسترسی سریع -->
            <div class="col-xl-6 col-12">
                <div class="row g-4">
                    <!-- آمار امروز -->
                    <div class="col-12">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bx bx-calendar me-2 text-primary"></i>
                                    آمار امروز من
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-4">
                                        <div class="text-center p-3 bg-light rounded-3">
                                            <span class="d-block text-muted small">اخبار جدید</span>
                                            <h5 class="text-primary mb-0">{{ $todayPosts }}</h5>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center p-3 bg-light rounded-3">
                                            <span class="d-block text-muted small">یادداشت‌های جدید</span>
                                            <h5 class="text-warning mb-0">{{ $todayNotes }}</h5>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-center p-3 bg-light rounded-3">
                                            <span class="d-block text-muted small">مصاحبه‌های جدید</span>
                                            <h5 class="text-danger mb-0">{{ $todayInterviews }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- دسترسی سریع -->
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bx bx-link-alt me-2 text-primary"></i>
                                    دسترسی سریع
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-6 col-md-3">
                                        <a href="{{ route('author.posts.create') }}"
                                            class="btn btn-outline-primary w-100 py-3">
                                            <i class="bx bx-plus-circle d-block fs-4"></i>
                                            <span class="d-block mt-1">خبر جدید</span>
                                        </a>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <a href="{{ route('author.posts.index') }}"
                                            class="btn btn-outline-info w-100 py-3">
                                            <i class="bx bx-list-ul d-block fs-4"></i>
                                            <span class="d-block mt-1">لیست اخبار</span>
                                        </a>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <a href="#"
                                            class="btn btn-outline-success w-100 py-3">
                                            <i class="bx bx-user d-block fs-4"></i>
                                            <span class="d-block mt-1">پروفایل</span>
                                        </a>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <a href="{{ route('author.posts.create') }}?type=note"
                                            class="btn btn-outline-warning w-100 py-3">
                                            <i class="bx bx-pencil d-block fs-4"></i>
                                            <span class="d-block mt-1">یادداشت جدید</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
