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

                @php $shareUrl = $post->short_url ?? $post->shortUrl ?? route('post.short', $post->short_code); @endphp
                <!-- اشتراک‌گذاری — شورت‌لینک کوتاه -->
                <div class="share-section mt-4 pt-3 border-top">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="fw-bold me-1" style="font-size:0.95rem;">اشتراک‌گذاری:</span>
                        <a href="https://telegram.me/share/url?url={{ urlencode($shareUrl) }}&text={{ urlencode(mb_substr($post->title,0,80)) }}" target="_blank" rel="noopener" class="btn-share btn-telegram" title="اشتراک در تلگرام" aria-label="تلگرام">
                            <i class="fab fa-telegram-plane"></i>
                            <span>تلگرام</span>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode(mb_substr($post->title,0,80).' - '.$shareUrl) }}" target="_blank" rel="noopener" class="btn-share btn-whatsapp" title="اشتراک در واتس‌اپ" aria-label="واتس‌اپ">
                            <i class="fab fa-whatsapp"></i>
                            <span>واتس‌اپ</span>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode(mb_substr($post->title,0,80)) }}&url={{ urlencode($shareUrl) }}" target="_blank" rel="noopener" class="btn-share btn-twitter" title="اشتراک در ایکس" aria-label="ایکس">
                            <i class="fab fa-x-twitter"></i>
                            <span>ایکس</span>
                        </a>
                        <button type="button" id="copyLinkBtn" onclick="copyLink()" class="btn-share btn-copy" aria-label="کپی لینک" data-short-url="{{ $shareUrl }}">
                            <i class="bi bi-link-45deg"></i>
                            <span id="copyLinkText">کپی لینک</span>
                        </button>
                    </div>
                </div>

                <div id="copyToast" class="copy-toast" role="status" aria-live="polite">
                    <div class="copy-toast-icon"><i class="bi bi-check-lg"></i></div>
                    <div class="copy-toast-body">
                        <strong>کپی شد!</strong>
                        <span>لینک کوتاه خبر در کلیپ‌بورد ذخیره شد</span>
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

@push('styles')
<style>
.share-section .btn-share{display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:999px;font-size:.84rem;font-weight:600;text-decoration:none;border:1px solid transparent;transition:all .2s;cursor:pointer;line-height:1}
.btn-telegram{background:#229ED9;color:#fff;border-color:#229ED9}.btn-telegram:hover{background:#1c8ac0;color:#fff;transform:translateY(-1px);box-shadow:0 4px 12px rgba(34,158,217,.3)}
.btn-whatsapp{background:#25D366;color:#fff;border-color:#25D366}.btn-whatsapp:hover{background:#1fb85a;color:#fff;transform:translateY(-1px);box-shadow:0 4px 12px rgba(37,211,102,.3)}
.btn-twitter{background:#000;color:#fff;border-color:#000}.btn-twitter:hover{background:#222;color:#fff;transform:translateY(-1px)}
.btn-copy{background:#fff;color:#495057;border-color:#dee2e6}.btn-copy:hover{background:#f8f9fa;border-color:#adb5bd;color:#212529;transform:translateY(-1px)}
.btn-copy.copied{background:#198754!important;color:#fff!important;border-color:#198754!important}
.copy-toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(16px);background:#1e293b;color:#fff;padding:12px 18px;border-radius:12px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 32px rgba(0,0,0,.2);opacity:0;visibility:hidden;transition:all .35s cubic-bezier(.34,1.56,.64,1);z-index:9999;min-width:280px;max-width:90vw}
.copy-toast.show{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0)}
.copy-toast-icon{width:32px;height:32px;border-radius:50%;background:#22c55e;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px}
.copy-toast-body strong{display:block;font-size:.9rem;line-height:1}.copy-toast-body span{font-size:.78rem;opacity:.85}
</style>
@endpush
@push('scripts')
<script>
function copyLink(){
    const btn = document.getElementById('copyLinkBtn');
    const url = (btn && btn.dataset.shortUrl) ? btn.dataset.shortUrl : window.location.href;
    const txt = document.getElementById('copyLinkText');
    const toast = document.getElementById('copyToast');
    function showSuccess(){
        if(toast){ toast.classList.add('show'); setTimeout(()=>toast.classList.remove('show'), 2600); }
        if(btn && txt){ const orig = txt.textContent; txt.textContent='کپی شد ✓'; btn.classList.add('copied'); setTimeout(()=>{ txt.textContent=orig; btn.classList.remove('copied'); }, 2200); }
        if(navigator.vibrate) try{ navigator.vibrate(40);}catch(e){}
    }
    function fallbackCopy(){
        const ta=document.createElement('textarea'); ta.value=url; ta.style.position='fixed'; ta.style.opacity='0'; document.body.appendChild(ta); ta.select();
        try{ document.execCommand('copy'); showSuccess(); }catch(e){ prompt('لینک خبر:', url); }
        document.body.removeChild(ta);
    }
    if(navigator.clipboard && window.isSecureContext){
        navigator.clipboard.writeText(url).then(showSuccess).catch(fallbackCopy);
    } else { fallbackCopy(); }
}
</script>
@endpush