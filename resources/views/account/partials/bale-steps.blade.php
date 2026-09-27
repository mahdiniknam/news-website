{{-- راهنمای گام‌به‌گام اتصال ربات بله --}}
@php
    $botUsername = \App\Models\Setting::get('bale_bot_username', 'didebaneshahr_news_bot');
@endphp

<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="bx bx-list-ol me-2 text-primary"></i>
            مراحل اتصال
        </h5>
    </div>
    <div class="card-body">
        <ul class="list-unstyled mb-0">
            <li class="d-flex align-items-start mb-3">
                <span class="badge bg-label-primary rounded-circle p-2 me-3">۱</span>
                <div>
                    در پیام‌رسان <strong>بله</strong> به ربات
                    <a href="https://ble.ir/{{ $botUsername }}" target="_blank" rel="noopener"
                        class="fw-bold" dir="ltr">{{ '@' . $botUsername }}</a>
                    بروید و دکمه <strong>شروع / Start</strong> را بزنید.
                </div>
            </li>
            <li class="d-flex align-items-start mb-3">
                <span class="badge bg-label-primary rounded-circle p-2 me-3">۲</span>
                <div>
                    کد بالا را کپی کنید و در ربات <strong>ارسال</strong> کنید.
                </div>
            </li>
            <li class="d-flex align-items-start">
                <span class="badge bg-label-success rounded-circle p-2 me-3">۳</span>
                <div>
                    ربات پیام «حساب شما متصل شد» را ارسال می‌کند؛ همین! از این پس
                    اطلاع‌رسانی‌ها به شما ارسال می‌شود.
                </div>
            </li>
        </ul>
        <div class="alert alert-light border mt-3 mb-0 small">
            <i class="bx bx-shield me-1"></i>
            کد اتصال یک‌بارمصرف است و فقط ۱۵ دقیقه اعتبار دارد. اگر منقضی شد، کد جدید بگیرید.
        </div>
    </div>
</div>
