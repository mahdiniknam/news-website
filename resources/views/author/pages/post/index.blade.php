@extends('admin.layout.master')

@section('title', 'مدیریت پست')

@section('admin-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h4 class="mb-1">مدیریت پست</h4>
            <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">
                <i class="bx bx-plus me-1"></i>
                خبر جدید
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- اضافه کردن ستون وضعیت و نمایش وضعیت‌ها -->

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>عنوان</th>
                                <th>نویسنده</th>
                                <th>وضعیت</th>
                                <th>نوع</th>
                                <th>تاریخ انتشار</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $post->title }}</strong>
                                        @if ($post->special)
                                            <span class="badge bg-danger ms-1">اسلاید</span>
                                        @endif
                                        @if ($post->is_featured)
                                            <span class="badge bg-warning ms-1">ویژه</span>
                                        @endif
                                    </td>
                                    <td>{{ $post->author->name ?? '-' }}</td>
                                    <td>{!! $post->status_badge !!}</td>
                                    <td>
                                        @if ($post->type === 'news')
                                            <span class="badge bg-info">خبر</span>
                                            @elseif ($post->type === 'note')
                                            <span class="badge bg-success">یادداشت</span>
                                            @else
                                            <span class="badge bg-secondary">مصاحبه</span>
                                        @endif
                                     
                                    </td>
                                   
                                    <td>{{ $post->published_at ? verta($post->published_at)->format('Y/m/d') : '-' }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                data-bs-toggle="dropdown">
                                                عملیات
                                            </button>
                                            <div class="dropdown-menu">
                                                @if ($post->canEdit())
                                                    <a href="{{ route('admin.posts.edit', $post) }}" class="dropdown-item">
                                                        <i class="bx bx-edit me-1"></i> ویرایش
                                                    </a>
                                                @endif

                                                @can('manage-posts')
                                                    {{-- فقط ادمین‌ها --}}
                                                    @if ($post->status === 'pending')
                                                        <button class="dropdown-item text-success"
                                                            onclick="approvePost({{ $post->id }})">
                                                            <i class="bx bx-check me-1"></i> تایید
                                                        </button>
                                                        <button class="dropdown-item text-danger"
                                                            onclick="rejectPost({{ $post->id }})">
                                                            <i class="bx bx-x me-1"></i> رد
                                                        </button>
                                                    @endif
                                                @endcan

                                                @can('manage-posts')
                                                    @if ($post->status === 'approved' || $post->status === 'draft')
                                                        <button class="dropdown-item text-primary"
                                                            onclick="publishPost({{ $post->id }})">
                                                            <i class="bx bx-upload me-1"></i> انتشار
                                                        </button>
                                                    @endif
                                                @endcan

                                                @if ($post->canDelete())
                                                    <form action="{{ route('admin.posts.destroy', $post) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger"
                                                            onclick="return confirm('آیا از حذف این خبر مطمئن هستید؟')">
                                                            <i class="bx bx-trash me-1"></i> حذف
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">هیچ پستی یافت نشد</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $posts->links() }}
            </div>
        </div>
    </div>

    <!-- مودال رد خبر -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">رد پست</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">دلیل رد <span class="text-danger">*</span></label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                        <button type="submit" class="btn btn-danger">رد پست</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleSpecial(id) {
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: 'این خبر به عنوان اسلاید ویژه نمایش داده خواهد شد',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله',
                cancelButtonText: 'انصراف'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/posts/${id}/toggle-special`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'موفق!',
                                text: response.message,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطا!',
                                text: xhr.responseJSON?.message || 'خطا در تغییر وضعیت ویژه'
                            });
                        }
                    });
                }
            });
        }

        function approvePost(id) {
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: 'این خبر تایید خواهد شد',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله، تایید کن',
                cancelButtonText: 'انصراف'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/posts/${id}/approve`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'موفق!',
                                text: response.message,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطا!',
                                text: xhr.responseJSON?.message || 'خطا در تایید خبر'
                            });
                        }
                    });
                }
            });
        }

        function rejectPost(id) {
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            const form = document.getElementById('rejectForm');
            form.action = `/admin/posts/${id}/reject`;
            modal.show();
        }

        function publishPost(id) {
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: 'این خبر منتشر خواهد شد',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله، انتشار',
                cancelButtonText: 'انصراف'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/posts/${id}/publish`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'موفق!',
                                text: response.message,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطا!',
                                text: xhr.responseJSON?.message || 'خطا در انتشار خبر'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
