@extends($layout ?? 'author.layout.master')

@section('author-title', 'اتصال ربات بله')
@section('admin-title', 'اتصال ربات بله')

@section($section ?? 'author-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="mb-4">
                    <h4 class="mb-1">اتصال حساب به ربات بله</h4>
                    <p class="text-muted mb-0">
                        با اتصال حساب، نتیجه تایید یا رد خبرها به‌صورت خودکار در ربات بله برای شما ارسال می‌شود.
                    </p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($chatId)
                    <!-- وضعیت: متصل -->
                    <div class="card border-success">
                        <div class="card-body text-center py-5">
                            <div class="avatar avatar-lg bg-label-success mb-3">
                                <i class="bx bx-check-circle fs-2"></i>
                            </div>
                            <h5 class="mb-2">حساب شما متصل است ✅</h5>
                            <p class="text-muted mb-4">
                                شناسه گفتگوی شما: <code dir="ltr">{{ $chatId }}</code>
                                <br>
                                اطلاع‌رسانی‌های تایید/رد خبر از طریق ربات برای شما ارسال می‌شود.
                            </p>
                            <form action="{{ route('account.bale.unlink') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger"
                                    onclick="return confirm('آیا از قطع اتصال مطمئن هستید؟')">
                                    <i class="bx bx-link-off me-1"></i>
                                    قطع اتصال
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif ($activeCode)
                    <!-- وضعیت: کد فعال دارد -->
                    <div class="card">
                        <div class="card-body py-5 text-center">
                            <h5 class="mb-4">کد اتصال شما</h5>
                            <div class="d-flex justify-content-center mb-3">
                                <div class="bg-label-primary rounded-3 px-5 py-3"
                                    style="font-size: 2.2rem; font-weight: 800; letter-spacing: 6px; direction: ltr;">
                                    {{ $activeCode->code }}
                                </div>
                            </div>
                            <p class="text-muted">
                                <i class="bx bx-time-five me-1"></i>
                                اعتبار تا: {{ verta($activeCode->expires_at)->format('H:i') }}
                            </p>
                        </div>
                    </div>
                    @include('account.partials.bale-steps')
                @else
                    <!-- وضعیت: بدون کد -->
                    <div class="card">
                        <div class="card-body py-5 text-center">
                            <div class="avatar avatar-lg bg-label-primary mb-3">
                                <i class="bx bx-message-square-dots fs-2"></i>
                            </div>
                            <h5 class="mb-2">هنوز به ربات بله متصل نشده‌اید</h5>
                            <p class="text-muted mb-4">
                                برای اتصال، یک کد یک‌بارمصرف بگیرید و آن را در ربات ارسال کنید.
                            </p>

                            @if ($botConfigured)
                                <form action="{{ route('account.bale.code') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bx bx-code me-1"></i>
                                        گرفتن کد اتصال
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-warning d-inline-block mb-0">
                                    <i class="bx bx-error me-1"></i>
                                    ربات بله هنوز توسط مدیر سایت تنظیم نشده است.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if (session('new_code'))
                    {{-- کد تازه ساخته‌شده در همین درخواست --}}
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            location.reload();
                        });
                    </script>
                @endif

            </div>
        </div>
    </div>
@endsection
