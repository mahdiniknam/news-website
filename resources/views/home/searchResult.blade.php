@extends('home.layout.master')

@section('title', 'نتایج جستجو')

@section('content')
    <div class="container-fluid px-3 px-md-4">
        <!-- هدر -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-2 mb-sm-0">
                <i class="bi bi-search text-primary me-2"></i>
                نتایج جستجو
            </h4>
            <span class="badge bg-primary fs-6 px-3 py-2">
                {{ $posts->total() }} نتیجه
            </span>
        </div>

        <!-- عبارت جستجو -->
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="bi bi-info-circle me-2 fs-5"></i>
            <span>نتایج جستجو برای: <strong>"{{ $query }}"</strong></span>
        </div>

        <!-- اگر نتیجه‌ای وجود نداشت -->
        @if($posts->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-search" style="font-size: 64px; color: var(--bs-secondary-color);"></i>
                <h4 class="mt-3">هیچ نتیجه‌ای یافت نشد</h4>
                <p class="text-muted">برای عبارت "{{ $query }}" هیچ خبری پیدا نشد.</p>
                <a href="{{ route('home') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-arrow-right me-1"></i>
                    بازگشت به صفحه اصلی
                </a>
            </div>
        @else
            <!-- لیست نتایج -->
            <div class="row g-3">
                @foreach($posts as $post)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                            <!-- تصویر -->
                            @if ($post->featured_image)
                                <div class="position-relative overflow-hidden" style="height: 160px;">
                                    <img src="{{ asset('storage/' . $post->featured_image) }}"
                                        class="card-img-top w-100 h-100 object-fit-cover"
                                        alt="{{ $post->title }}">
                                </div>
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                                    <i class="bi bi-image text-secondary" style="font-size: 48px;"></i>
                                </div>
                            @endif

                            <!-- برچسب -->
                            <div class="position-absolute top-0 start-0 m-2">
                                @if($post->category && $post->category->slug === 'mosabehe')
                                    <span class="badge bg-danger">
                                        <i class="bi bi-mic"></i> مصاحبه
                                    </span>
                                @elseif($post->category && $post->category->slug === 'yaddasht')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-pencil-square"></i> یادداشت
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        <i class="bi bi-newspaper"></i> خبر
                                    </span>
                                @endif
                            </div>

                            <!-- محتوای کارت -->
                            <div class="card-body p-3">
                                @if($post->category)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary mb-2">
                                        {{ $post->category->name }}
                                    </span>
                                @endif

                                <h6 class="card-title fw-bold mb-2 text-truncate-2">
                                    <a href="{{ route('post.show', $post->slug) }}" 
                                       class="text-decoration-none text-dark hover-primary">
                                        {{ $post->title }}
                                    </a>
                                </h6>

                                <p class="card-text small text-muted mb-2 text-truncate-3">
                                    {{ $post->excerpt }}
                                </p>

                                <div class="d-flex flex-wrap justify-content-between align-items-center small text-muted">
                                    <span>
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ verta($post->published_at)->format('Y/m/d') }}
                                    </span>
                                    <span>
                                        <i class="bi bi-eye me-1"></i>
                                        {{ number_format($post->view_count) }}
                                    </span>
                                </div>
                                <div class="small text-muted mt-1">
                                    <i class="bi bi-person me-1"></i>
                                    {{ $post->author->name ?? 'ناشناس' }}
                                </div>
                            </div>

                            <!-- فوتر کارت -->
                            <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
                                <a href="{{ route('post.show', $post->slug) }}" 
                                   class="btn btn-primary btn-sm w-100">
                                    مشاهده <i class="bi bi-arrow-left ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- صفحه‌بندی -->
            <div class="row mt-4">
                <div class="col-12 d-flex justify-content-center">
                    {{ $posts->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }
    
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.12) !important;
    }
    
    .hover-primary:hover {
        color: var(--bs-primary) !important;
    }
    
    .transition {
        transition: all 0.3s ease;
    }
    
    .object-fit-cover {
        object-fit: cover;
    }
    
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.5;
        max-height: 3em;
    }
    
    .text-truncate-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.6;
        max-height: 4.8em;
    }
    
    .pagination {
        gap: 5px;
    }
    
    .pagination .page-link {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.9rem;
        color: var(--bs-body-color);
        transition: all 0.2s;
    }
    
    .pagination .page-link:hover {
        background-color: var(--bs-primary);
        color: white;
        border-color: var(--bs-primary);
    }
    
    .pagination .active .page-link {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
        color: white;
    }
    
    .pagination .disabled .page-link {
        opacity: 0.5;
        cursor: not-allowed;
    }

    @media (max-width: 576px) {
        .card-title {
            font-size: 0.95rem !important;
        }
        .card-text {
            font-size: 0.8rem !important;
        }
        .btn-sm {
            font-size: 0.75rem !important;
            padding: 0.25rem 0.5rem !important;
        }
        .badge {
            font-size: 0.65rem !important;
        }
        .pagination .page-link {
            padding: 0.3rem 0.6rem;
            font-size: 0.8rem;
        }
    }
    
    @media (min-width: 576px) and (max-width: 768px) {
        .card-title {
            font-size: 1rem !important;
        }
    }
    
    @media (min-width: 992px) {
        .card-title {
            font-size: 1.05rem !important;
        }
    }
</style>
@endpush