@extends($isAuthorView ?? 'author.layout.master')

@section('author-title', 'اطلاعات من')

@section('author-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mb-4">
            <h4 class="mb-1">اطلاعات من</h4>
            <p class="text-muted mb-0">نام، تصویر و رمز عبور خود را از این بخش ویرایش کنید.</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card text-center">
                    <div class="card-body py-4">
                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}"
                                class="rounded-circle mb-3"
                                style="width: 110px; height: 110px; object-fit: cover;">
                        @else
                            <div class="avatar-initial rounded-circle bg-label-primary d-flex align-items-center justify-content-center mx-auto mb-3"
                                style="width: 110px; height: 110px; font-size: 44px;">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                        @endif
                        <h5 class="mb-1">{{ $user->name }}</h5>
                        <p class="text-muted mb-2">{{ $user->email }}</p>
                        <span class="badge bg-label-info">
                            {{ $user->roles->first()?->display_name ?? $user->roles->first()?->name ?? 'کاربر' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">ویرایش اطلاعات</h5>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('account.profile.update') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="name">نام و نام خانوادگی
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $user->name) }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="email">ایمیل (غیرقابل تغییر)</label>
                                    <input type="email" id="email" class="form-control" value="{{ $user->email }}"
                                        disabled>
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label" for="bio">بیوگرافی</label>
                                    <textarea id="bio" name="bio" rows="3"
                                        class="form-control @error('bio') is-invalid @enderror"
                                        placeholder="چند خط درباره خودتان...">{{ old('bio', $user->bio) }}</textarea>
                                    @error('bio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="avatar">تصویر پروفایل</label>
                                    <input type="file" id="avatar" name="avatar"
                                        class="form-control @error('avatar') is-invalid @enderror">
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted">jpeg, png, jpg, gif, webp — حداکثر ۵ مگابایت</small>
                                </div>
                            </div>

                            <div class="divider my-3">
                                <div class="divider-text">تغییر رمز عبور (اختیاری)</div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="current_password">رمز فعلی</label>
                                    <input type="password" id="current_password" name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="password">رمز جدید</label>
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="password_confirmation">تکرار رمز جدید</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> ذخیره تغییرات
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
