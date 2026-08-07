@extends('home.layout.master')

@section('title', $metaTitle ?? $post->title . ' - یادداشت')

@section('meta_description', $metaDescription ?? '')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <!-- مسیر -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('notes') }}">یادداشت‌ها</a></li>
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

            <!-- جزئیات یادداشت -->
            <article class="post-detail">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-pencil-square"></i> یادداشت
                    </span>
                </div>

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
                             style="max-height: 400px; object-fit: cover;">
                    </div>
                @endif

                <!-- محتوای یادداشت -->
                <div class="post-content" style="line-height: 2.5; font-size: 1.1rem; color: var(--bs-body-color);">
                    {!! $post->content !!}
                </div>

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
                        <button onclick="copyLink()" class="btn btn-sm btn-secondary">
                            <i class="fas fa-link me-1"></i> کپی لینک
                        </button>
                    </div>
                </div>
            </article>

            <!-- یادداشت‌های مرتبط -->
            @if(isset($relatedPosts) && $relatedPosts->isNotEmpty())
                <section class="related-posts mt-5 pt-4 border-top">
                    <h4 class="fw-bold mb-3">
                        <i class="bi bi-arrow-left-circle text-primary me-2"></i>
                        یادداشت‌های مرتبط
                    </h4>
                    <div class="row">
                        @foreach($relatedPosts as $related)
                            <div class="col-md-6 mb-3">
                                <div class="news-card">
                                    <div class="news-card-body p-3">
                                        <h6 class="news-card-title">
                                            <a href="{{ route('post.show', $related->slug) }}">
                                                {{ $related->title }}
                                            </a>
                                        </h6>
                                        <div style="font-size: 0.8rem; color: var(--bs-secondary-color);">
                                            <i class="bi bi-calendar3"></i> 
                                            {{ verta($related->published_at)->format('Y/m/d') }}
                                        </div>
                                        <p class="news-card-excerpt" style="font-size: 0.85rem; margin-top: 5px;">
                                            {{ $related->excerpt }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <!-- سایدبار -->
        <div class="col-lg-4">
            <!-- یادداشت‌های اخیر -->
            <div class="notes-sidebar mb-4">
                <h5 class="notes-sidebar-title">
                    <i class="bi bi-clock-history text-primary me-2"></i>
                    آخرین یادداشت‌ها
                </h5>
                @php
                    $latestNotes = \App\Models\Post::where('status', 'published')
                        ->whereHas('category', function($q) {
                            $q->where('slug', 'yaddasht')
                              ->orWhere('name', 'یادداشت');
                        })
                        ->latest('published_at')
                        ->limit(5)
                        ->get();
                @endphp
                @foreach($latestNotes as $note)
                    <div class="note-item">
                        <div class="note-item-title">
                            <a href="{{ route('post.show', $note->slug) }}">{{ $note->title }}</a>
                        </div>
                        <div class="note-item-meta">
                            <i class="bi bi-calendar3"></i> 
                            {{ verta($note->published_at)->format('Y/m/d') }}
                        </div>
                    </div>
                @endforeach
                <div class="text-center mt-2">
                    <a href="{{ route('notes') }}" class="btn btn-outline-primary-custom btn-sm">
                        مشاهده همه یادداشت‌ها
                    </a>
                </div>
            </div>

            <!-- دسته‌بندی‌ها -->
            @if(isset($categories))
                <div class="notes-sidebar">
                    <h5 class="notes-sidebar-title">
                        <i class="bi bi-tags text-primary me-2"></i>
                        دسته‌بندی‌ها
                    </h5>
                    <ul class="list-unstyled">
                        @foreach($categories as $category)
                            <li class="mb-2">
                                <a href="{{ route('category.show', $category->slug) }}" 
                                   class="text-decoration-none text-muted d-flex justify-content-between">
                                    <span>{{ $category->name }}</span>
                                    <span class="badge bg-primary rounded-pill">
                                        {{ $category->posts_count ?? 0 }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'موفق!',
                text: 'لینک با موفقیت کپی شد!',
                timer: 2000,
                showConfirmButton: false
            });
        }).catch(() => {
            const input = document.createElement('input');
            input.value = window.location.href;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            
            Swal.fire({
                icon: 'success',
                title: 'موفق!',
                text: 'لینک با موفقیت کپی شد!',
                timer: 2000,
                showConfirmButton: false
            });
        });
    }
</script>
@endpush