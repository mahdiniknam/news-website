@extends('home.layout.master')

@section('title', $metaTitle ?? $post->title . ' - مصاحبه')

@section('meta_description', $metaDescription ?? '')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <!-- مسیر -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">خانه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('interviews') }}">مصاحبه‌ها</a></li>
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

            <!-- جزئیات مصاحبه -->
            <article class="post-detail">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-danger">
                        <i class="bi bi-mic"></i> مصاحبه
                    </span>
                </div>

                <h1 class="fw-bold mb-3">{{ $post->title }}</h1>

                <!-- اطلاعات مصاحبه‌شونده -->
                <div class="interviewer-info mb-4 p-3 bg-primary bg-opacity-10 rounded">
                    <div class="d-flex align-items-center gap-3">
                        @if($post->author && $post->author->avatar)
                            <img src="{{ asset('storage/' . $post->author->avatar) }}" 
                                 alt="{{ $post->author->name }}" 
                                 class="rounded-circle"
                                 style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                                 style="width: 60px; height: 60px;">
                                <i class="fas fa-user fa-2x text-white"></i>
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-0">{{ $post->author->name ?? 'ناشناس' }}</h5>
                            <div class="text-muted small">
                                <i class="bi bi-calendar3"></i> 
                                {{ verta($post->published_at)->format('Y/m/d H:i') }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تصویر شاخص -->
                @if($post->featured_image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $post->featured_image) }}" 
                             alt="{{ $post->title }}" 
                             class="img-fluid rounded w-100"
                             style="max-height: 450px; object-fit: cover;">
                    </div>
                @endif

                <!-- محتوای مصاحبه -->
                <div class="post-content" style="line-height: 2.2; font-size: 1.05rem;">
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

            <!-- مصاحبه‌های مرتبط -->
            @if(isset($relatedPosts) && $relatedPosts->isNotEmpty())
                <section class="related-posts mt-5 pt-4 border-top">
                    <h4 class="fw-bold mb-3">
                        <i class="bi bi-arrow-left-circle text-primary me-2"></i>
                        مصاحبه‌های مرتبط
                    </h4>
                    <div class="row">
                        @foreach($relatedPosts as $related)
                            <div class="col-md-6 mb-3">
                                <div class="interview-card d-flex gap-3 align-items-center p-3 border rounded">
                                    @if($related->author && $related->author->avatar)
                                        <img src="{{ asset('storage/' . $related->author->avatar) }}" 
                                             alt="{{ $related->author->name }}" 
                                             class="rounded-circle"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-user fa-1x text-white"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('post.show', $related->slug) }}" 
                                               class="text-decoration-none">
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
        </div>

        <!-- سایدبار -->
        <div class="col-lg-4">
            <!-- مصاحبه‌های اخیر -->
            <div class="notes-sidebar mb-4">
                <h5 class="notes-sidebar-title">
                    <i class="bi bi-clock-history text-primary me-2"></i>
                    آخرین مصاحبه‌ها
                </h5>
                @php
                    $latestInterviews = \App\Models\Post::where('status', 'published')
                        ->whereHas('category', function($q) {
                            $q->where('slug', 'mosabehe')
                              ->orWhere('name', 'مصاحبه');
                        })
                        ->latest('published_at')
                        ->limit(5)
                        ->get();
                @endphp
                @foreach($latestInterviews as $interview)
                    <div class="note-item">
                        <div class="note-item-title">
                            <a href="{{ route('post.show', $interview->slug) }}">{{ $interview->title }}</a>
                        </div>
                        <div class="note-item-meta">
                            <i class="bi bi-person"></i> {{ $interview->author->name ?? 'ناشناس' }}
                            <span class="mx-1">|</span>
                            <i class="bi bi-calendar3"></i> 
                            {{ verta($interview->published_at)->format('Y/m/d') }}
                        </div>
                    </div>
                @endforeach
                <div class="text-center mt-2">
                    <a href="{{ route('interviews') }}" class="btn btn-outline-primary-custom btn-sm">
                        مشاهده همه مصاحبه‌ها
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