@extends('admin.layout.master')

@section('admin-title')
    ایجاد کاربر جدید
@endsection

@section('admin-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mb-4">
            <h4 class="mb-1">ایجاد کاربر جدید</h4>
        </div>

        <form action="{{ route('admin.admins.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card mb-2">
                <div class="card-body">
                    <!-- ردیف اول: نام و ایمیل -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">نام مدیر</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror" placeholder="محمد محمدی">
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">ایمیل مدیر</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror" placeholder="test@gmail.com">
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- ردیف دوم: رمز عبور و تکرار رمز عبور -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="password" class="form-label">رمز عبور مدیر</label>
                            <input id="password" name="password" type="password"
                                class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">تکرار رمز عبور</label>
                            <input id="password_confirmation" name="password_confirmation" type="password"
                                class="form-control @error('password_confirmation') is-invalid @enderror">
                            @error('password_confirmation')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- ردیف سوم: بیوگرافی و تصویر -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="bio" class="form-label">بیوگرافی مدیر (اختیاری)</label>
                            <textarea id="bio" name="bio" rows="3" class="form-control @error('bio') is-invalid @enderror"
                                placeholder="توضیحات درباره مدیر...">{{ old('bio') }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="avatar" class="form-label">تصویر مدیر</label>
                            <input id="avatar" name="avatar" type="file"
                                class="form-control @error('avatar') is-invalid @enderror">
                            @error('avatar')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted">فرمت‌های مجاز: jpeg, png, jpg, gif (حداکثر 10 مگابایت)</small>
                        </div>
                    </div>

                    <!-- اطلاع‌رسانی اتصال بله -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-start mb-0" role="alert">
                                <i class="bx bx-message-square-dots fs-4 me-2"></i>
                                <div>
                                    <strong>اتصال به ربات بله:</strong>
                                    نیازی به وارد کردن شناسه نیست! پس از ساخت کاربر، خود کاربر از پنل خودش
                                    (بخش «اتصال ربات بله») یک کد یک‌بارمصرف می‌گیرد و در ربات ارسال می‌کند؛
                                    اتصال خودکار انجام می‌شود و اعلان‌های تایید/رد خبر برایش ارسال می‌گردد.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ردیف جدید: انتخاب نقش‌ها -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label">نقش‌های مدیر</label>
                            <div class="row">
                                @foreach ($roles as $role)
                                    <div class="col-md-3 col-sm-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input @error('roles') is-invalid @enderror"
                                                type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                id="role_{{ $role->id }}"
                                                {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
                                                {{ $role->display_name ?? $role->name }}
                                                @if ($role->description)
                                                    <br>
                                                    <small class="text-muted">{{ $role->description }}</small>
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('roles')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                            @error('roles.*')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- ردیف چهارم: وضعیت فعال/غیرفعال -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" name="is_active" type="checkbox" id="flexSwitchCheckChecked"
                                    value="1" checked>
                                <label class="form-check-label" for="flexSwitchCheckChecked">فعال / غیرفعال</label>
                                @error('is_active')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    ثبت کاربر
                </button>

                <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary">
                    انصراف
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
@endpush
