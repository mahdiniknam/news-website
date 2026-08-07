@extends('home.layout.master')

@section('title', 'همه اخبار')

@section('content')
    <div class="container-fluid px-3 px-md-4">
        <!-- هدر -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-2 mb-sm-0">
                <i class="bi bi-newspaper text-primary me-2"></i>
                همه اخبار
            </h4>
            <span class="badge bg-primary fs-6 px-3 py-2">
                {{ $posts->total() }} خبر
            </span>
        </div>

        <!-- فیلترها -->
        <div class="filter-section mb-4 p-3 bg-light rounded">
            <form action="{{ route('news.search') }}" method="GET" class="row g-2">
                <div class="col-12 col-sm-6 col-md-5">
                    <select name="category" class="form-select form-select-sm">
                        <option value="">همه دسته‌بندی‌ها</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->slug }}"
                                {{ request('category') == $category->slug ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-8 col-sm-4 col-md-5">
                    <input type="text" name="q" class="form-control form-control-sm" placeholder="جستجو..."
                        value="{{ request('q') }}">
                </div>
                <div class="col-4 col-sm-2 col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-search me-1"></i>
                        <span class="d-none d-sm-inline">فیلتر</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- لیست اخبار با کارت‌های کوچک -->
        <div class="row g-3">
            @forelse($posts as $post)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                        <!-- تصویر -->
                        @if ($post->featured_image)
                            <div class="position-relative overflow-hidden" style="height: 180px;">
                                <img src="{{ asset('storage/' . $post->featured_image) }}"
                                    class="card-img-top w-100 h-100 object-fit-cover" alt="{{ $post->title }}">
                                @if ($post->is_featured)
                                    <span
                                        class="position-absolute top-0 start-0 bg-warning text-dark px-2 py-1 m-2 rounded small">
                                        <i class="bi bi-star-fill"></i> ویژه
                                    </span>
                                @endif
                                @if ($post->special)
                                    <span
                                        class="position-absolute top-0 end-0 bg-danger text-white px-2 py-1 m-2 rounded small">
                                        <i class="bi bi-play-fill"></i> اسلاید
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="bi bi-image text-secondary" style="font-size: 48px;"></i>
                            </div>
                        @endif

                        <!-- محتوای کارت -->
                        <div class="card-body p-3">
                            <!-- دسته‌بندی -->
                            @if ($post->category)
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-2">
                                    {{ $post->category->name }}
                                </span>
                            @endif

                            <!-- عنوان -->
                            <h6 class="card-title fw-bold mb-2 text-truncate-2">
                                <a href="{{ route('post.show', $post->slug) }}"
                                    class="text-decoration-none text-dark hover-primary">
                                    {{ $post->title }}
                                </a>
                            </h6>

                            <!-- خلاصه -->
                            <p class="card-text small text-muted mb-2 text-truncate-3">
                                {{ $post->excerpt }}
                            </p>

                            <!-- متا اطلاعات -->
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
                            <a href="{{ route('post.show', $post->slug) }}" class="btn btn-primary btn-sm w-100">
                                مشاهده خبر <i class="bi bi-arrow-left ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 48px; color: var(--bs-secondary-color);"></i>
                        <h5 class="mt-3">هیچ خبری یافت نشد</h5>
                        <p class="text-muted">با تغییر فیلترها، می‌توانید اخبار مورد نظر خود را پیدا کنید.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- صفحه‌بندی -->
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                {{ $posts->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* استایل‌های کارت */
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12) !important;
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

        /* صفحه‌بندی سفارشی */
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

        /* ریسپانسیو */
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
