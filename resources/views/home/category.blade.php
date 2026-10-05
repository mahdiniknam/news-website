@extends('home.layout.master')

@section('title', $category->name)

@section('content')
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">خانه</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-2 mb-sm-0">
            <i class="bi bi-tag text-primary me-2"></i>
            {{ $category->name }}
        </h4>
        <span class="badge bg-primary fs-6 px-3 py-2">
            {{ $posts->total() }} خبر
        </span>
    </div>

    @if($posts->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox" style="font-size: 48px; color: var(--bs-secondary-color);"></i>
            <h5 class="mt-3">خبری در این دسته‌بندی یافت نشد</h5>
            <a href="{{ route('news') }}" class="btn btn-primary mt-2">مشاهده همه اخبار</a>
        </div>
    @else
        <div class="row g-3">
            @foreach($posts as $post)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        @if ($post->featured_image)
                            <div class="position-relative overflow-hidden" style="height: 180px;">
                                <img src="{{ asset('storage/' . $post->featured_image) }}" class="w-100 h-100 object-fit-cover" alt="{{ $post->title }}">
                            </div>
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="bi bi-image text-secondary" style="font-size: 48px;"></i>
                            </div>
                        @endif
                        <div class="card-body p-3">
                            @if ($post->category)
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-2">{{ $post->category->name }}</span>
                            @endif
                            <h6 class="fw-bold mb-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;line-height:1.5;">
                                <a href="{{ route('post.show', $post->slug) }}" class="text-decoration-none text-dark">{{ $post->title }}</a>
                            </h6>
                            <p class="small text-muted mb-2" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;line-height:1.6;">{{ $post->excerpt }}</p>
                            <div class="d-flex justify-content-between small text-muted">
                                <span><i class="bi bi-calendar3 me-1"></i>{{ verta($post->published_at)->format('Y/m/d') }}</span>
                                <span><i class="bi bi-eye me-1"></i>{{ number_format($post->view_count) }}</span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-3">
                            <a href="{{ route('post.show', $post->slug) }}" class="btn btn-primary btn-sm w-100">مشاهده خبر <i class="bi bi-arrow-left ms-1"></i></a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $posts->links('pagination::bootstrap-5') }}
        </div>
    @endif
@endsection
