@extends('home.layout.master')

@section('title', 'صفحه اصلی - پایگاه خبری')

@section('content')
    @if ($slides->isNotEmpty() && isset($news) && $news->isNotEmpty())
        <div class="breaking-ticker d-flex align-items-center gap-3 mb-3">
            <span class="badge-breaking"><i class="bi bi-lightning-fill me-1"></i>خبر فوری</span>
            <span class="flex-grow-1 text-truncate fw-medium small">
                {{ Str::limit(strip_tags($news->first()->title), 110, '...') }}
            </span>
            <a href="{{ route('post.show', $news->first()->slug) }}" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3 py-1 flex-shrink-0">
                مشاهده
            </a>
        </div>
    @endif

    <!-- اسلایدر اخبار ویژه -->
    <section class="hero-slider">
        <div id="newsSlider" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @foreach ($slides as $key => $slide)
                    <button type="button" data-bs-target="#newsSlider" data-bs-slide-to="{{ $key }}"
                        class="{{ $key == 0 ? 'active' : '' }}" aria-current="{{ $key == 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $key + 1 }}"></button>
                @endforeach
            </div>
            <div class="carousel-inner">
                @foreach ($slides as $key => $slide)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}"
                        style="background-image: url('{{ asset('storage/' . $slide->featured_image) }}');">
                        <div class="carousel-caption">
                            <h3>{{ $slide->title }}</h3>
                            <p>{!! Str::limit(strip_tags($slide->content), 120, '...') !!}</p>
                            <a href="{{ route('post.show', $slide->slug) }}" class="btn btn-primary btn-sm rounded-pill px-4">
                                مشاهده خبر <i class="bi bi-arrow-left ms-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#newsSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">قبلی</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#newsSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">بعدی</span>
            </button>
        </div>
    </section>

    <div class="row">
        <!-- ستون اصلی اخبار -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>آخرین اخبار</h4>
                <a href="{{ route('news') }}" class="btn btn-outline-primary-custom btn-sm">مشاهده همه</a>
            </div>

            <div class="row">
                @foreach ($news as $item)
                    <div class="col-md-6 mb-4">
                        <div class="news-card h-100">
                            @if ($item->featured_image)
                                <img src="{{ asset('storage/' . $item->featured_image) }}" class="news-card-img"
                                    alt="{{ $item->title }}">
                            @else
                                <div class="news-card-img bg-secondary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-3x text-white-50"></i>
                                </div>
                            @endif
                            <div class="news-card-body d-flex flex-column">
                                <h5 class="news-card-title">
                                    <a href="{{ route('post.show', $item->slug) }}">{{ Str::limit($item->title, 70, '...') }}</a>
                                </h5>
                                @if ($item->category)
                                    <span class="badge bg-primary bg-opacity-10 text-primary align-self-start mb-2">{{ $item->category->name }}</span>
                                @endif
                                <p class="news-card-excerpt flex-grow-1">{!! Str::limit(strip_tags($item->content), 150, '...') !!}</p>
                                <div class="news-card-meta flex-wrap">
                                    <span><i class="bi bi-calendar3"></i> {{ verta($item->published_at)->format('Y/m/d') }}</span>
                                    <span><i class="bi bi-person"></i> {{ $item->author->name ?? 'ناشناس' }}</span>
                                    @if ($item->category)
                                        <span><i class="bi bi-tag"></i> {{ $item->category->name }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('post.show', $item->slug) }}" class="btn btn-outline-primary-custom btn-sm mt-3 align-self-start">
                                    مشاهده خبر <i class="bi bi-arrow-left ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $news->links('pagination::bootstrap5') }}
            </div>
        </div>

        <!-- سایدبار -->
        <div class="col-lg-4">
            <!-- یادداشت‌ها -->
            <div class="notes-sidebar mb-4">
                <h5 class="notes-sidebar-title">
                    <i class="bi bi-pencil-square text-primary me-2"></i>
                    یادداشت‌ها
                </h5>
                @forelse($notes as $note)
                    <div class="note-item">
                        <div class="note-item-title">
                            <a href="{{ route('post.show', $note->slug) }}">{{ Str::limit($note->title, 70, '...') }}</a>
                        </div>
                        <p class="small text-muted mb-1" style="line-height:1.7;">{{ Str::limit(strip_tags($note->content), 90, '...') }}</p>
                        <div class="note-item-meta">
                            <i class="bi bi-calendar3"></i> {{ verta($note->published_at)->format('Y/m/d') }}
                            <span class="mx-2">|</span>
                            <i class="bi bi-person"></i> {{ $note->author->name ?? 'ناشناس' }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted">هیچ یادداشتی یافت نشد</p>
                @endforelse

                <div class="text-center mt-3">
                    <a href="{{ route('notes') }}" class="btn btn-outline-primary-custom btn-sm">مشاهده همه یادداشت‌ها</a>
                </div>
            </div>

            <!-- مصاحبه‌ها -->
            <div class="notes-sidebar">
                <h5 class="notes-sidebar-title">
                    <i class="bi bi-mic text-primary me-2"></i>
                    مصاحبه‌ها
                </h5>
                @forelse($interviews as $interview)
                    <div class="note-item">
                        <div class="note-item-title">
                            <a href="{{ route('post.show', $interview->slug) }}">{{ Str::limit($interview->title, 70, '...') }}</a>
                        </div>
                        <p class="small text-muted mb-1" style="line-height:1.7;">{{ Str::limit(strip_tags($interview->content), 90, '...') }}</p>
                        <div class="note-item-meta">
                            <i class="bi bi-calendar3"></i> {{ verta($interview->published_at)->format('Y/m/d') }}
                            <span class="mx-2">|</span>
                            <i class="bi bi-person"></i> {{ $interview->author->name ?? 'ناشناس' }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted">هیچ مصاحبه‌ای یافت نشد</p>
                @endforelse

                <div class="text-center mt-3">
                    <a href="{{ route('interviews') }}" class="btn btn-outline-primary-custom btn-sm">مشاهده همه مصاحبه‌ها</a>
                </div>
            </div>
        </div>
    </div>
@endsection
