@extends('home.layout.master')

@section('title', 'صفحه اصلی - پایگاه خبری')

@section('content')
    <!-- اسلایدر اخبار ویژه -->
    <section class="hero-slider">
        <div id="newsSlider" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @foreach($slides as $key => $slide)
                    <button type="button" data-bs-target="#newsSlider" data-bs-slide-to="{{ $key }}" 
                            class="{{ $key == 0 ? 'active' : '' }}" aria-current="{{ $key == 0 ? 'true' : 'false' }}"
                            aria-label="Slide {{ $key + 1 }}"></button>
                @endforeach
            </div>
            <div class="carousel-inner">
                @foreach($slides as $key => $slide)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}" 
                         style="background-image: url('{{ asset('storage/' . $slide->featured_image) }}');">
                        <div class="carousel-caption">
                            <h3>{{ $slide->title }}</h3>
                            <p>{{ $slide->excerpt }}</p>
                            <a href="{{ route('post.show', $slide->slug) }}" class="btn btn-primary btn-sm">
                                مشاهده خبر
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
                @foreach($news as $item)
                    <div class="col-md-6 mb-4">
                        <div class="news-card">
                            @if($item->featured_image)
                                <img src="{{ asset('storage/' . $item->featured_image) }}" 
                                     class="news-card-img" alt="{{ $item->title }}">
                            @else
                                <div class="news-card-img bg-secondary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-3x text-white-50"></i>
                                </div>
                            @endif
                            <div class="news-card-body">
                                <h5 class="news-card-title">
                                    <a href="{{ route('post.show', $item->slug) }}">{{ $item->title }}</a>
                                </h5>
                                <div class="news-card-meta">
                                    <span><i class="bi bi-calendar3"></i> {{ verta($item->published_at)->format('Y/m/d') }}</span>
                                    <span><i class="bi bi-person"></i> {{ $item->author->name ?? 'ناشناس' }}</span>
                                    @if($item->category)
                                        <span><i class="bi bi-tag"></i> {{ $item->category->name }}</span>
                                    @endif
                                </div>
                                <p class="news-card-excerpt">{{ $item->excerpt }}</p>
                                <a href="{{ route('post.show', $item->slug) }}" class="btn btn-outline-primary-custom btn-sm">
                                    مشاهده خبر <i class="bi bi-arrow-left ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center">
                {{ $news->links() }}
            </div>
        </div>

        <!-- سایدبار یادداشت‌ها -->
        <div class="col-lg-4">
            <div class="notes-sidebar">
                <h5 class="notes-sidebar-title">
                    <i class="bi bi-pencil-square text-primary me-2"></i>
                    یادداشت‌ها
                </h5>
                @forelse($notes as $note)
                    <div class="note-item">
                        <div class="note-item-title">
                            <a href="{{ route('post.show', $note->slug) }}">{{ $note->title }}</a>
                        </div>
                        <div class="note-item-meta">
                            <i class="bi bi-calendar3"></i> {{ verta($note->published_at)->format('Y/m/d') }}
                            <span class="mx-2">|</span>
                            <i class="bi bi-person"></i> {{ $note->author->name ?? 'ناشناس' }}
                        </div>
                        <div class="note-item-excerpt mt-1" style="font-size: 0.85rem; color: var(--bs-secondary-color);">
                            {{ $note->excerpt }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted">هیچ یادداشتی یافت نشد</p>
                @endforelse

                <div class="text-center mt-3">
                    <a href="{{ route('notes') }}" class="btn btn-outline-primary-custom btn-sm">مشاهده همه یادداشت‌ها</a>
                </div>
            </div>
        </div>
    </div>

    <!-- بخش مصاحبه‌ها -->
    <section class="interview-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold"><i class="bi bi-mic text-primary me-2"></i>مصاحبه‌ها</h4>
            <a href="{{ route('interviews') }}" class="btn btn-outline-primary-custom btn-sm">مشاهده همه</a>
        </div>

        <div class="row">
            @foreach($interviews as $interview)
                <div class="col-md-6">
                    <div class="interview-card">
                        @if($interview->author->avatar)
                            <img src="{{ asset('storage/' . $interview->author->avatar) }}" 
                                 alt="{{ $interview->author->name }}" 
                                 class="interview-avatar">
                        @else
                            <div class="interview-avatar bg-secondary d-flex align-items-center justify-content-center">
                                <i class="fas fa-user fa-2x text-white"></i>
                            </div>
                        @endif
                        <div class="interview-content">
                            <div class="interview-title">
                                <a href="{{ route('post.show', $interview->slug) }}">{{ $interview->title }}</a>
                            </div>
                            <div class="interview-excerpt">{{ $interview->excerpt }}</div>
                            <div style="font-size: 0.8rem; color: var(--bs-secondary-color); margin-top: 5px;">
                                <i class="bi bi-calendar3"></i> {{ verta($interview->published_at)->format('Y/m/d') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection