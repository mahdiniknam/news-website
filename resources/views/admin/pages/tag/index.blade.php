@extends('admin.layout.master')
@section('admin-title')
    تگ نوشته ها
@endsection
@section('admin-content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="col-12">
            <div class="card mb-4">
                <h5 class="card-header heading-color">ایجاد تگ</h5>
                <div class="card-body">
                    <form action="{{ route('admin.tags.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
                                <label class="form-label" for="phone-number-mask">عنوان تگ</label>
                                <div class="input-group">
                                    <input type="text" name="name" id="phone-number-mask"
                                        class="form-control phone-number-mask text-start" dir="rtl"
                                        value="{{ old('name') }}">
                                </div>
                                @error('name')
                                    <div class="text-danger mt-1 small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <div class="form-password-toggle">
                                    <label class="form-label" for="multicol-country">وضعیت تگ</label>
                                    <select id="status" class="form-select" data-allow-clear="true" name="status">
                                        <option value="">وضعیت</option>
                                        @foreach (\app\Models\Tag::statuses() as $key => $value)
                                            <option
                                                value="{{ $key }}"{{ (int) old('status') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="text-danger mt-1 small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <!-- Blocks -->
                            <div class="pt-4">
                                <button type="submit" class="btn btn-primary me-sm-3 me-1">ثبت</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Users List Table -->
            <div class="card">
                <h5 class="card-header heading-color">لیست تگ ها </h5>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>شناسه </th>
                                <th>نام </th>
                                <th>وضعیت</th>
                                <th>تنظیمات</th>

                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach ($tags as $tag)
                                <tr>
                                    <td> {{ $tag->id }}</td>
                                    <td>{{ $tag->name }} </td>
                                    <td>
                                        @if ($tag->status == 'active')
                                            <span class="badge bg-label-primary me-1 ">{{ $tag->status_label }}</span>
                                        @else
                                            <span class="badge bg-label-danger me-1 ">{{ $tag->status_label }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="btn btn-icon btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#editModal" data-id="{{ $tag->id }}"
                                                data-title="{{ $tag->title }}" data-status="{{ $tag->status }}"
                                                onclick="fillEditModal(this)">
                                                <i class="bx bx-edit"></i>
                                            </button>

                                            <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-sm btn-outline-danger"
                                                    onclick="return confirm('آیا از حذف تگ «{{ $tag->title }}» مطمئن هستید؟')">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $tags->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>

    <!-- Add New Credit Card Modal -->
    <!-- مودال ویرایش -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ویرایش تگ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="tag_id" id="edit_tag_id">

                        <div class="mb-3">
                            <label class="form-label">عنوان تگ</label>
                            <input type="text" name="name" id="edit_name" class="form-control"
                                placeholder="عنوان تگ را وارد کنید">
                            <div class="invalid-feedback" id="edit_name_error"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">وضعیت</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="">انتخاب وضعیت</option>
                                @foreach (\App\Models\Tag::statuses() as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="edit_status_error"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="button" class="btn btn-primary" id="saveEditBtn" onclick="updateTag()">
                        <span id="saveText">ذخیره تغییرات</span>
                        <span id="saveSpinner" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--/ Add New Credit Card Modal -->
@endsection


@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // تنظیم CSRF Token برای Ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        // تابع پر کردن مودال با اطلاعات تگ
        function fillEditModal(element) {
            const id = element.dataset.id;
            const title = element.dataset.name;
            const status = element.dataset.status;

            // پر کردن فیلدها
            document.getElementById('edit_tag_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_status').value = status;

            // تنظیم اکشن فرم
            document.getElementById('editForm').action = `/admin/tags/${id}`;

            // حذف خطاهای قبلی
            document.getElementById('edit_name').classList.remove('is-invalid');
            document.getElementById('edit_status').classList.remove('is-invalid');
            document.getElementById('edit_name_error').textContent = '';
            document.getElementById('edit_status_error').textContent = '';
        }

        // تابع آپدیت تگ
        function updateTag() {
            const form = document.getElementById('editForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            const id = document.getElementById('edit_tag_id').value;

            // نمایش اسپینر
            document.getElementById('saveText').classList.add('d-none');
            document.getElementById('saveSpinner').classList.remove('d-none');
            document.getElementById('saveEditBtn').disabled = true;

            // حذف خطاهای قبلی
            document.getElementById('edit_name').classList.remove('is-invalid');
            document.getElementById('edit_status').classList.remove('is-invalid');
            document.getElementById('edit_name_error').textContent = '';
            document.getElementById('edit_status_error').textContent = '';

            // ارسال درخواست Ajax
            $.ajax({
                url: '{{ route('admin.tags.update', ':id') }}'.replace(':id', id),
                type: 'PUT', // یا PUT
                data: {
                    _method: 'PUT',
                    _token: '{{ csrf_token() }}',
                    name: data.name,
                    status: data.status
                },
                success: function(response) {
                    // پیام موفقیت
                    Swal.fire({
                        icon: 'success',
                        title: 'موفق!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // بستن مودال
                    bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();

                    // رفرش صفحه بعد از 1.5 ثانیه
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                },
                error: function(xhr) {
                    // مخفی کردن اسپینر
                    document.getElementById('saveText').classList.remove('d-none');
                    document.getElementById('saveSpinner').classList.add('d-none');
                    document.getElementById('saveEditBtn').disabled = false;

                    if (xhr.status === 422) {
                        // نمایش خطاهای اعتبارسنجی
                        const errors = xhr.responseJSON.errors;

                        if (errors.title) {
                            document.getElementById('edit_title').classList.add('is-invalid');
                            document.getElementById('edit_title_error').textContent = errors.title[0];
                        }

                        if (errors.status) {
                            document.getElementById('edit_status').classList.add('is-invalid');
                            document.getElementById('edit_status_error').textContent = errors.status[0];
                        }
                    } else {
                        // خطای عمومی
                        Swal.fire({
                            icon: 'error',
                            title: 'خطا!',
                            text: xhr.responseJSON?.message || 'خطا در ویرایش تگ',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                }
            });
        }

        // رویداد Enter در فیلدها
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                const modal = document.getElementById('editModal');
                if (modal.classList.contains('show')) {
                    e.preventDefault();
                    updateTag();
                }
            }
        });
    </script>
@endpush
