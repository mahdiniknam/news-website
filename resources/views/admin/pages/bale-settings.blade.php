@extends('admin.layout.master')

@section('admin-title', 'تنظیمات ربات بله')

@section('admin-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mb-4">
            <h4 class="mb-1">
                <i class="bx bx-bot me-1"></i>
                تنظیمات ربات بله
            </h4>
            <p class="text-muted mb-0">
                توکن ربات را از <b dir="ltr">@BotFather</b> در بله بگیرید و اینجا وارد کنید.
                توکن به‌صورت رمزنگاری‌شده در دیتابیس ذخیره می‌شود.
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

        <div class="row g-4">
            <div class="col-xl-8">
                <!-- وضعیت ربات -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">وضعیت فعلی</h5>
                        @if ($tokenSet)
                            <span class="badge bg-label-success">
                                <i class="bx bx-check-circle me-1"></i>
                                توکن تنظیم شده
                                ({{ $tokenSource === 'db' ? 'پنل' : 'فایل env' }})
                            </span>
                        @else
                            <span class="badge bg-label-danger">
                                <i class="bx bx-x-circle me-1"></i>
                                تنظیم نشده
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($tokenSet)
                            <div class="row text-center">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="p-3 bg-label-secondary rounded-3">
                                        <span class="d-block text-muted small mb-1">ربات</span>
                                        @if ($botUsername)
                                            <strong dir="ltr">{{ '@' . $botUsername }}</strong>
                                        @else
                                            <span class="text-danger small">
                                                خطا در اتصال: {{ $botError }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-label-secondary rounded-3">
                                        <span class="d-block text-muted small mb-1">Webhook</span>
                                        @if ($webhookUrl)
                                            <span class="text-success">
                                                <i class="bx bx-check me-1"></i> فعال
                                            </span>
                                        @else
                                            <span class="text-warning">
                                                <i class="bx bx-minus-circle me-1"></i> ثبت نشده
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted mb-0">
                                هنوز توکنی ثبت نشده است. ربات برای اطلاع‌رسانی تایید/رد خبر به مدیران و خبرنگاران
                                لازم است.
                            </p>
                        @endif
                    </div>
                </div>

                <!-- فرم توکن -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">ثبت توکن ربات</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.bale.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="bot_token">توکن ربات</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-key"></i></span>
                                    <input type="text" id="bot_token" name="bot_token" dir="ltr"
                                        class="form-control @error('bot_token') is-invalid @enderror"
                                        value="{{ old('bot_token') }}"
                                        placeholder="123456789:AbCdEfGhIjKlMnOpQrStUvWxYz">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i>
                                        ذخیره و تست
                                    </button>
                                </div>
                                @error('bot_token')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    توکن صحت‌سنجی می‌شود و در صورت موفقیت، webhook هم به‌صورت خودکار ثبت می‌گردد.
                                </small>
                            </div>

                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="set_webhook"
                                    name="set_webhook" value="1" checked>
                                <label class="form-check-label" for="set_webhook">
                                    ثبت خودکار webhook (برای اتصال خودکار حساب کاربران از طریق کد)
                                </label>
                            </div>
                        </form>

                        @if ($tokenSource === 'db')
                            <hr>
                            <form action="{{ route('admin.bale.store') }}" method="POST"
                                onsubmit="return confirm('آیا از حذف توکن ربات مطمئن هستید؟')">
                                @csrf
                                <input type="hidden" name="remove_token" value="1">
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bx bx-trash me-1"></i>
                                    حذف توکن ذخیره‌شده
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <!-- آمار اتصال‌ها -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-link-alt me-1"></i>
                            اتصال کاربران
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>کدهای فعال در انتظار</span>
                            <span class="badge bg-label-warning">{{ $activeCodes }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>کاربران متصل</span>
                            <span class="badge bg-label-success">{{ $linkedAdmins->count() }}</span>
                        </div>
                        @if ($linkedAdmins->isNotEmpty())
                            <hr>
                            <div class="list-group list-group-flush">
                                @foreach ($linkedAdmins as $linked)
                                    <div class="d-flex justify-content-between align-items-center py-2">
                                        <div>
                                            <span class="fw-semibold">{{ $linked->name }}</span>
                                            <small class="d-block text-muted">
                                                {{ $linked->roles->first()?->name ?? 'کاربر' }}
                                            </small>
                                        </div>
                                        <code dir="ltr" class="small">{{ $linked->bale_chat_id }}</code>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- راهنما -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bx bx-help-circle me-1"></i>
                            راهنما
                        </h5>
                    </div>
                    <div class="card-body small text-muted">
                        <ol class="ps-3 mb-0">
                            <li class="mb-2">در بله به <b dir="ltr">@BotFather</b> پیام دهید و با دستور
                                <code dir="ltr">/newbot</code> ربات بسازید.</li>
                            <li class="mb-2">توکن دریافتی را در فرم بالا وارد و ذخیره کنید.</li>
                            <li class="mb-2">Webhook باید روی دامنه HTTPS فعال باشد (localhost پشتیبانی نمی‌شود).</li>
                            <li>کاربران از پنل خودشان کد می‌گیرند و به ربات ارسال می‌کنند تا متصل شوند.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
