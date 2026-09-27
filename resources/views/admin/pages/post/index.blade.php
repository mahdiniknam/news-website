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
                                    <td>{{ $post->id }}</td>
                                    <td>
                                        <strong>{{ Str::limit($post->title, 10, '...') }}</strong>
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

                                                @if ($post->canEdit())
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
                                                @endif

                                                @if ($post->canEdit())
                                                    @if ($post->status === 'approved' || $post->status === 'draft' || $post->status === 'pending')
                                                        <button class="dropdown-item text-primary"
                                                            onclick="publishPost({{ $post->id }})">
                                                            <i class="bx bx-upload me-1"></i> انتشار
                                                        </button>
                                                    @endif
                                                @endif

                                                @if ($post->canDelete())
                                                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST"
                                                        style="display: inline;">
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
                    <div class="d-flex justify-content-center mt-4">
                        {{ $posts->links('pagination::bootstrap5') }}
                    </div>
            </div>
        </div>
    </div>

    <!-- مودال رد خبر (خارج از dropdown و جدول) -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bx bx-x-circle text-danger me-2"></i>
                        رد خبر
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rejectForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                دلیل رد خبر <span class="text-danger">*</span>
                            </label>
                            <textarea name="rejection_reason" id="rejection_reason"
                                class="form-control @error('rejection_reason') is-invalid @enderror" rows="4"
                                placeholder="دلیل رد خبر را وارد کنید..." required></textarea>
                            @error('rejection_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">این دلیل برای نویسنده نمایش داده می‌شود</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bx bx-x me-1"></i> رد خبر
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // تنظیم CSRF Token برای درخواست‌های Ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

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
                        url: '{{ route('admin.posts.approve', ':id') }}'.replace(':id', id),
                        type: 'POST',
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'موفق!',
                                text: response.message || 'خبر با موفقیت تایید شد.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطا!',
                                text: xhr.responseJSON?.message || 'خطا در تایید خبر',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        }

        function rejectPost(id) {
            // تنظیم اکشن فرم
            const form = document.getElementById('rejectForm');
            form.action = '{{ route('admin.posts.reject', ':id') }}'.replace(':id', id);

            // پاک کردن مقدار قبلی
            document.getElementById('rejection_reason').value = '';
            document.getElementById('rejection_reason').classList.remove('is-invalid');

            // نمایش مودال
            const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
            modal.show();
        }

        // ارسال فرم رد با Ajax (به جای submit معمولی)
        document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const reason = document.getElementById('rejection_reason').value.trim();

            if (!reason) {
                document.getElementById('rejection_reason').classList.add('is-invalid');
                Swal.fire({
                    icon: 'error',
                    title: 'خطا!',
                    text: 'لطفاً دلیل رد را وارد کنید',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            if (reason.length < 5) {
                document.getElementById('rejection_reason').classList.add('is-invalid');
                Swal.fire({
                    icon: 'error',
                    title: 'خطا!',
                    text: 'دلیل رد باید حداقل ۵ کاراکتر باشد',
                    confirmButtonColor: '#d33'
                });
                return;
            }

            // بستن مودال قبل از نمایش SweetAlert
            const modalElement = document.getElementById('rejectModal');
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }

            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: 'این خبر با دلیل ذکر شده رد خواهد شد',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'بله، رد کن',
                cancelButtonText: 'انصراف'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('rejectForm');
                    const formData = new FormData(form);

                    $.ajax({
                        url: form.action,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'موفق!',
                                text: response.message || 'خبر با موفقیت رد شد.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMsg = xhr.responseJSON?.message || 'خطا در رد خبر';
                            if (xhr.status === 422) {
                                const errors = xhr.responseJSON.errors;
                                if (errors.rejection_reason) {
                                    errorMsg = errors.rejection_reason[0];
                                }
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'خطا!',
                                text: errorMsg,
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                } else {
                    // اگر کاربر انصراف داد، مودال را دوباره باز کن
                    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
                    modal.show();
                }
            });
        });

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
                        url: '{{ route('admin.posts.publish', ':id') }}'.replace(':id', id),
                        type: 'POST',
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'موفق!',
                                text: response.message || 'خبر با موفقیت منتشر شد.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'خطا!',
                                text: xhr.responseJSON?.message || 'خطا در انتشار خبر',
                                confirmButtonColor: '#d33'
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
