@extends('admin.layout.master')

@section('admin-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">مدیریت ادمین ها</h4>

            <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                ایجاد ادمین جدید
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
                            <th>شناسه </th>
                            <th>تصویر </th>
                            <th>نام </th>
                            <th>ایمیل</th>
                            <th>وضعیت</th>
                            <th>عضویت</th>
                            <th>بیوگرافی</th>
                            <th class="text-center">عملیات</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($admins as $admin)
                            <tr>
                                <td>{{ $admin->id }}</td>
                                @if ($admin->avatar)
                                    <td>
                                        <img src="{{ asset('storage/' . $admin->avatar) }} " alt="{{ $admin->name }}"
                                            width="30" height="30" class="rounded-circle">
                                    </td>
                                @else
                                    <td>
                                        <img src="../../assets/img/avatars/1.png" alt="{{ $admin->name }}" width="30"
                                            height="30" class="rounded-circle">
                                    </td>
                                @endif
                                <td>{{ Str::limit($admin->name, 15, '...') }}</td>
                                <td>{{ Str::limit($admin->email, 15, '...') }}</td>

                                <td>
                                    @if ($admin->is_active == true)
                                        <span class="badge bg-label-primary">
                                            فعال
                                        </span>
                                    @else
                                        <span class="badge bg-label-danger">
                                            غیرفعال
                                        </span>
                                    @endif

                                </td>
                                <td>
                                    <span class="badge bg-label-info">

                                        {{ $admin->roles->first()->name ?? 'بدون نقش' }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($admin->bio, 8, '...') }}</td>
                                @if (!$admin->isSuperAdmin())
                                    <td class="text-center">
                                        <a href="{{ route('admin.admins.edit', $admin) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            ویرایش
                                        </a>
                                    </td>
                                @else
                                    <td colspan="3" class="text-center py-4">
                                        عدم دسترسی
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4">
                                    هنوز هیچ مدیری ایجاد نشده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
