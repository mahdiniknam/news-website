@extends('author.layout.master')

@section('author-title', 'مدیریت پست')

@section('author-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mb-4 d-flex justify-content-between align-items-center">
            <h4 class="mb-1">مدیریت پست</h4>
            <a href="{{ route('author.posts.create') }}" class="btn btn-primary">
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
                                        <strong>{{ Str::limit($post->title,10,'...') }}</strong>
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
                                                
                                                    <a href="{{ route('author.posts.edit', $post) }}" class="dropdown-item">
                                                        <i class="bx bx-edit me-1"></i> ویرایش
                                                    </a>
                                             

                                                {{-- نمایش دلیل رد با SweetAlert --}}
                                                @if ($post->status === 'rejected' && $post->rejection_reason)
                                                    <button class="dropdown-item text-danger"
                                                        onclick="showRejectionReason({{ $post->id }})">
                                                        <i class="bx bx-info-circle me-1"></i> نمایش دلیل رد
                                                    </button>
                                                @endif

                                              
                                                    <form action="{{ route('author.posts.destroy', $post) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger"
                                                            onclick="return confirm('آیا از حذف این خبر مطمئن هستید؟')">
                                                            <i class="bx bx-trash me-1"></i> حذف
                                                        </button>
                                                    </form>
                                                
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
@endsection

@push('author-scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // تنظیم CSRF Token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        // نمایش دلیل رد با SweetAlert
        function showRejectionReason(id) {
            // نمایش لودینگ
            Swal.fire({
                title: 'در حال دریافت اطلاعات...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // دریافت دلیل رد با Ajax
            $.ajax({
                url: '{{ route('author.posts.rejection-reason', ':id') }}'.replace(':id', id),
                type: 'GET',
                success: function(response) {
                    Swal.fire({
                        icon: 'info',
                        title: 'دلیل رد خبر',
                        html: `
                            <div class="text-start">
                                <div class="alert alert-danger p-3 rounded">
                                    <strong class="d-block mb-2">📌 دلیل رد:</strong>
                                    <p class="mb-0">${response.reason || 'دلیلی ثبت نشده است'}</p>
                                </div>
                                <div class="d-flex justify-content-between text-muted small mt-3">
                                    <span>
                                        <i class="bx bx-calendar me-1"></i>
                                        تاریخ رد: ${response.rejected_at || '-'}
                                    </span>
                                    <span>
                                        <i class="bx bx-user me-1"></i>
                                        تایید کننده: مدیر سایت
                                    </span>
                                </div>
                            </div>
                        `,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'تایید'
                    });
                },
                error: function(xhr) {
                    let errorMsg = 'خطا در دریافت دلیل رد';
                    if (xhr.status === 404) {
                        errorMsg = 'دلیلی برای رد این خبر یافت نشد';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'خطا!',
                        text: errorMsg,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'تایید'
                    });
                }
            });
        }

        // تابع حذف با SweetAlert (به جای confirm معمولی)
        function deletePost(id, form) {
            Swal.fire({
                title: 'آیا مطمئن هستید؟',
                text: 'این خبر به طور کامل حذف خواهد شد!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'بله، حذف شود',
                cancelButtonText: 'انصراف'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
@endpush