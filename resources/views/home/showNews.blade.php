@extends('home.layout.master')

@section('title', $metaTitle ?? $post->title)

@section('meta_description', $metaDescription ?? '')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <!-- مسیر -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">خانه</a></li>
                    @if($post->category)
                        <li class="breadcrumb-item">
                            <a href="{{ route('category.show', $post->category->slug) }}">
                                {{ $post->category->name }}
                            </a>
                        </li>
                    @endif
                    <li class="breadcrumb-item active" aria-current="page">{{ $post->title }}</li>
                </ol>
            </nav>

            <!-- جزئیات خبر -->
            <article class="post-detail">
                <h1 class="fw-bold mb-3">{{ $post->title }}</h1>

                <!-- متا اطلاعات -->
                <div class="post-meta mb-4">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <span>
                            <i class="bi bi-calendar3 text-primary"></i> 
                            {{ verta($post->published_at)->format('Y/m/d H:i') }}
                        </span>
                        <span>
                            <i class="bi bi-person text-primary"></i> 
                            {{ $post->author->name ?? 'ناشناس' }}
                        </span>
                        @if($post->category)
                            <span>
                                <i class="bi bi-tag text-primary"></i> 
                                <a href="{{ route('category.show', $post->category->slug) }}" 
                                   class="text-decoration-none">
                                    {{ $post->category->name }}
                                </a>
                            </span>
                        @endif
                        <span>
                            <i class="bi bi-eye text-primary"></i> 
                            {{ number_format($post->view_count) }} بازدید
                        </span>
                        <span>
                            <i class="bi bi-clock text-primary"></i> 
                            {{ $readingTime }}
                        </span>
                    </div>
                </div>

                <!-- تصویر شاخص -->
                @if($post->featured_image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $post->featured_image) }}" 
                             alt="{{ $post->title }}" 
                             class="img-fluid rounded w-100"
                             style="max-height: 500px; object-fit: cover;">
                        <div class="text-muted small mt-1">
                            <i class="bi bi-info-circle"></i> تصویر: {{ $post->title }}
                        </div>
                    </div>
                @endif

                <!-- محتوای خبر -->
                <div class="post-content" style="line-height: 2.2; font-size: 1.05rem;">
                    {!! $post->content !!}
                </div>

                <!-- برچسب‌ها -->
                @if($post->tags)
                    <div class="post-tags mt-4 pt-3 border-top">
                        <span class="fw-bold">برچسب‌ها:</span>
                        @foreach($post->tags as $tag)
                            <a href="#" class="badge bg-secondary text-decoration-none me-1">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif

                <!-- اشتراک‌گذاری -->
                <div class="share-buttons mt-4 pt-3 border-top">
                    <p class="fw-bold">اشتراک‌گذاری:</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="https://telegram.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" 
                           target="_blank" class="btn btn-sm btn-primary">
                            <i class="fab fa-telegram me-1"></i> تلگرام
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($post->title . ' - ' . url()->current()) }}" 
                           target="_blank" class="btn btn-sm btn-success">
                            <i class="fab fa-whatsapp me-1"></i> واتس‌اپ
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url()->current()) }}" 
                           target="_blank" class="btn btn-sm btn-info text-white">
                            <i class="fab fa-twitter me-1"></i> توییتر
                        </a>
                        <button onclick="copyLink()" class="btn btn-sm btn-secondary">
                            <i class="fas fa-link me-1"></i> کپی لینک
                        </button>
                    </div>
                </div>
            </article>

            <!-- اخبار مرتبط -->
            @if($relatedPosts->isNotEmpty())
                <section class="related-posts mt-5 pt-4 border-top">
                    <h4 class="fw-bold mb-3">
                        <i class="bi bi-arrow-left-circle text-primary me-2"></i>
                        اخبار مرتبط
                    </h4>
                    <div class="row">
                        @foreach($relatedPosts as $related)
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="news-card">
                                    @if($related->featured_image)
                                        <img src="{{ asset('storage/' . $related->featured_image) }}" 
                                             class="news-card-img" 
                                             style="height: 150px;"
                                             alt="{{ $related->title }}">
                                    @endif
                                    <div class="news-card-body p-3">
                                        <h6 class="news-card-title" style="font-size: 0.9rem;">
                                            <a href="{{ route('post.show', $related->slug) }}">
                                                {{ $related->title }}
                                            </a>
                                        </h6>
                                        <div style="font-size: 0.75rem; color: var(--bs-secondary-color);">
                                            <i class="bi bi-calendar3"></i> 
                                            {{ verta($related->published_at)->format('Y/m/d') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- نظرات (اختیاری) -->
            {{-- @include('partials.comments') --}}
        </div>

        <!-- سایدبار -->
        <div class="col-lg-4">
            <!-- دسته‌بندی‌ها -->
            <div class="notes-sidebar mb-4">
                <h5 class="notes-sidebar-title">
                    <i class="bi bi-tags text-primary me-2"></i>
                    دسته‌بندی‌ها
                </h5>
                <ul class="list-unstyled">
                    @foreach($categories as $category)
                        <li class="mb-2">
                            <a href="{{ route('category.show', $category->slug) }}" 
                               class="text-decoration-none text-muted">
                                {{ $category->name }}
                                <span class="badge bg-primary rounded-pill float-end">
                                    {{ $category->posts_count ?? 0 }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- پربازدیدترین‌ها -->
            <div class="notes-sidebar">
                <h5 class="notes-sidebar-title">
                    <i class="bi bi-fire text-primary me-2"></i>
                    پربازدیدترین‌ها
                </h5>
                @php
                    $popularPosts = \App\Models\Post::where('status', 'published')
                        ->latest('view_count')
                        ->limit(5)
                        ->get();
                @endphp
                @foreach($popularPosts as $popular)
                    <div class="note-item">
                        <div class="note-item-title">
                            <a href="{{ route('post.show', $popular->slug) }}">
                                {{ $popular->title }}
                            </a>
                        </div>
                        <div class="note-item-meta">
                            <i class="bi bi-eye"></i> {{ number_format($popular->view_count) }} بازدید
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('لینک با موفقیت کپی شد!');
        });
    }
</script>
@endpush