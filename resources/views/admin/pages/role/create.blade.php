@extends('admin.layout.master')

@section('admin-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mb-4">
            <h4 class="mb-1">ایجاد نقش جدید</h4>
        </div>

        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf

            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">نام نقش</label>

                        <input id="name" name="name" type="text" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror" placeholder="مثلاً editor یا author">

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">دسترسی‌های نقش</h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        @foreach ($permissions as $permission)
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                        value="{{ $permission->id }}" id="permission-{{ $permission->id }}"
                                        @checked(in_array($permission->id, old('permissions', [])))>

                                    <label class="form-check-label" for="permission-{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    ثبت نقش
                </button>

                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                    انصراف
                </a>
            </div>
        </form>
    </div>
@endsection
