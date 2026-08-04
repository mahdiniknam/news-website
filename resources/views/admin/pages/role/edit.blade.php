@extends('admin.layout.master')

@section('admin-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mb-4">
            <h4 class="mb-1">مدیریت دسترسی‌های نقش: {{ $role->name }}</h4>
        </div>

        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">انتخاب دسترسی‌ها</h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        @foreach ($permissions as $permission)
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                        value="{{ $permission->id }}" id="permission-{{ $permission->id }}"
                                        @checked(in_array($permission->id, old('permissions', $rolePermissionIds)))>

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
                    ذخیره تغییرات
                </button>

                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">
                    بازگشت
                </a>
            </div>
        </form>
    </div>
@endsection
