@extends('home.layout.master')

@section('title', 'صفحه یافت نشد — ۴۰۴')

@section('content')
<div class="py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <div class="mb-4">
                <span style="font-size:4rem;line-height:1;">🔍</span>
                <h1 class="fw-bold mt-3" style="font-size:3rem;">۴۰۴</h1>
                <h2 class="h4 fw-bold mb-3">صفحه مورد نظر یافت نشد</h2>
                <p class="text-muted mb-4" style="line-height:1.9;">
                    متأسفانه صفحه‌ای که به دنبال آن هستید وجود ندارد، منتقل شده یا حذف شده است.<br>
                    لینک را بررسی کنید یا از طریق دکمه‌های زیر ادامه دهید.
                </p>
            </div>

            <div class="d-flex gap-2 justify-content-center flex-wrap mb-4">
                <a href="{{ route('home') }}" class="btn btn-primary px-4">
                    <i class="bi bi-house me-1"></i> بازگشت به صفحه اصلی
                </a>
                <a href="{{ route('news') }}" class="btn btn-outline-primary px-4">
                    <i class="bi bi-newspaper me-1"></i> مشاهده اخبار
                </a>
                <button onclick="history.back()" class="btn btn-outline-secondary px-4">
                    <i class="bi bi-arrow-right me-1"></i> بازگشت
                </button>
            </div>

            @php
                $latest = \App\Models\Post::where('status','published')->latest('published_at')->limit(6)->get();
            @endphp
            @if($latest->isNotEmpty())
                <div class="text-start mt-5">
                    <h3 class="h5 fw-bold mb-3 text-center">
                        <i class="bi bi-fire text-danger me-1"></i> آخرین اخبار
                    </h3>
                    <div class="row g-3">
                        @foreach($latest as $post)
                            <div class="col-md-4 col-6">
                                <a href="{{ route('post.show', $post->slug) }}" class="text-decoration-none">
                                    <div class="news-card h-100">
                                        @if($post->featured_image)
                                            <img src="{{ asset('storage/'.$post->featured_image) }}" class="news-card-img" alt="{{ $post->title }}">
                                        @endif
                                        <div class="news-card-body">
                                            <h6 class="news-card-title" style="font-size:0.95rem;">{{ $post->title }}</h6>
                                            <div class="news-card-meta">
                                                <span><i class="bi bi-calendar3"></i> {{ verta($post->published_at)->format('Y/m/d') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
