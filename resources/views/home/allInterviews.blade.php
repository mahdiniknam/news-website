@extends('home.layout.master')

@section('title', 'همه مصاحبه‌ها')

@section('content')
    <div class="container-fluid px-3 px-md-4">
        <!-- هدر -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-2 mb-sm-0">
                <i class="bi bi-mic text-danger me-2"></i>
                همه مصاحبه‌ها
            </h4>
            <span class="badge bg-danger fs-6 px-3 py-2">
                {{ $posts->total() }} مصاحبه
            </span>
        </div>

        <!-- فیلترها -->
        {{-- <div class="filter-section mb-4 p-3 bg-light rounded">
            <form action="{{ route('interviews.search') }}" method="GET" class="row g-2">
                <div class="col-8 col-sm-9 col-md-10">
                    <input type="text" name="q" class="form-control form-control-sm"
                        placeholder="جستجو در مصاحبه‌ها..." value="{{ request('q') }}">
                </div>
                <div class="col-4 col-sm-3 col-md-2">
                    <button type="submit" class="btn btn-danger btn-sm w-100">
                        <i class="bi bi-search me-1"></i>
                        <span class="d-none d-sm-inline">جستجو</span>
                    </button>
                </div>
            </form>
        </div> --}}

        <!-- لیست مصاحبه‌ها با کارت‌های کوچک -->
        <div class="row g-3">
            @forelse($posts as $post)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition">
                        <!-- تصویر -->
                        @if ($post->author && $post->author->avatar)
                            <div class="position-relative overflow-hidden" style="height: 160px;">
                                <img src="{{ asset('storage/' . $post->author->avatar) }}"
                                    class="card-img-top w-100 h-100 object-fit-cover" alt="{{ $post->author->name }}">
                            </div>
                        @else
                            <div class="bg-danger bg-opacity-10 d-flex align-items-center justify-content-center"
                                style="height: 160px;">
                                <i class="bi bi-person-circle text-danger" style="font-size: 48px;"></i>
                            </div>
                        @endif

                        <!-- برچسب مصاحبه -->
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-danger">
                                <i class="bi bi-mic"></i> مصاحبه
                            </span>
                        </div>

                        <!-- محتوای کارت -->
                        <div class="card-body p-3">
                            <!-- دسته‌بندی -->
                            @if ($post->category)
                                <span class="badge bg-secondary bg-opacity-10 text-secondary mb-2">
                                    {{ $post->category->name }}
                                </span>
                            @endif

                            <!-- عنوان -->
                            <h6 class="card-title fw-bold mb-2 text-truncate-2">
                                <a href="{{ route('post.show', $post->slug) }}"
                                    class="text-decoration-none text-dark hover-danger">
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
                                    <i class="bi bi-person me-1"></i>
                                    {{ $post->author->name ?? 'ناشناس' }}
                                </span>
                                <span>
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ verta($post->published_at)->format('Y/m/d') }}
                                </span>
                            </div>
                            <div class="small text-muted mt-1">
                                <i class="bi bi-eye me-1"></i>
                                {{ number_format($post->view_count) }} بازدید
                            </div>
                        </div>

                        <!-- فوتر کارت -->
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
                            <a href="{{ route('post.show', $post->slug) }}" class="btn btn-danger btn-sm w-100">
                                مشاهده مصاحبه <i class="bi bi-arrow-left ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-mic-mute" style="font-size: 48px; color: var(--bs-secondary-color);"></i>
                        <h5 class="mt-3">هیچ مصاحبه‌ای یافت نشد</h5>
                        <p class="text-muted">در حال حاضر هیچ مصاحبه‌ای منتشر نشده است.</p>
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
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12) !important;
        }

        .hover-danger:hover {
            color: var(--bs-danger) !important;
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

        /* صفحه‌بندی */
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
            background-color: var(--bs-danger);
            color: white;
            border-color: var(--bs-danger);
        }

        .pagination .active .page-link {
            background-color: var(--bs-danger);
            border-color: var(--bs-danger);
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
