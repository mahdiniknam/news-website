@extends('admin.layout.master')

@section('admin-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">مدیریت نقش‌ها</h4>

            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                ایجاد نقش جدید
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>نام نقش</th>
                            <th>تعداد دسترسی‌ها</th>
                            <th class="text-center">عملیات</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>

                                <td>
                                    <span class="badge bg-label-primary">
                                        {{ $role->permissions->count() }} دسترسی
                                    </span>
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                                        مدیریت دسترسی‌ها
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">
                                    هنوز هیچ نقشی ایجاد نشده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
