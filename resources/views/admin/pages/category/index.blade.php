@extends('admin.layout.master')
@section('admin-title')
    دسته بندی ها
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
                <h5 class="card-header heading-color">ایجاد دسته بندی</h5>
                <div class="card-body">
                    <form action="{{ route('admin.categories.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <!-- نام دسته‌بندی -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="name">
                                    نام دسته‌بندی <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                    class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                    placeholder="نام دسته‌بندی را وارد کنید" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- دسته‌بندی والد -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="parent_id">
                                    دسته‌بندی والد (اختیاری)
                                </label>
                                <select name="parent_id" id="parent_id"
                                    class="form-select @error('parent_id') is-invalid @enderror">
                                    <option value="">بدون والد</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- وضعیت -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="status">
                                    وضعیت <span class="text-danger">*</span>
                                </label>
                                <select name="status" id="status"
                                    class="form-select @error('status') is-invalid @enderror">
                                    <option value="">انتخاب وضعیت</option>
                                    @foreach (\App\Models\Category::statuses() as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('status', '1') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ترتیب نمایش -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="sort_order">
                                    ترتیب نمایش
                                </label>
                                <input type="number" name="sort_order" id="sort_order"
                                    class="form-control @error('sort_order') is-invalid @enderror"
                                    value="{{ old('sort_order', 0) }}" placeholder="مثال: 0، 1، 2، ..." min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">اعداد کوچکتر، بالاتر نمایش داده می‌شوند</small>
                            </div>

                            <!-- دکمه‌ها -->
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i>
                                    ذخیره
                                </button>
                                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                                    <i class="bx bx-x me-1"></i>
                                    انصراف
                                </a>
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
                            @foreach ($categories as $cat)
                                <tr>
                                    <td> {{ $cat->id }}</td>
                                    <td>{{ $cat->name }} </td>
                                    <td>
                                        @if ($cat->status == true)
                                            <span class="badge bg-label-primary me-1 ">{{ $cat->status_label }}</span>
                                        @else
                                            <span class="badge bg-label-danger me-1 ">{{ $cat->status_label }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="btn btn-icon btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                data-bs-target="#editModal" data-id="{{ $cat->id }}"
                                                data-name="{{ $cat->name }}" data-status="{{ $cat->status }}"
                                                onclick="fillEditModal(this)">
                                                <i class="bx bx-edit"></i>
                                            </button>

                                            <form action="{{ route('admin.categories.destroy', $category) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('آیا از حذف دسته‌بندی «{{ $category->name }}» مطمئن هستید؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon btn-sm btn-outline-danger">
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
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            </div>

        </div>
    </div>

    <!-- Add New Credit Card Modal -->
    <!-- مودال ویرایش دسته‌بندی -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">ویرایش دسته‌بندی</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="POST">
                        @csrf
                        @method('PUT')

                        <input type="hidden" name="category_id" id="edit_category_id">

                        <!-- نام دسته‌بندی -->
                        <div class="mb-3">
                            <label class="form-label">نام دسته‌بندی <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control"
                                placeholder="نام دسته‌بندی را وارد کنید">
                            <div class="invalid-feedback" id="edit_name_error"></div>
                        </div>

                        <!-- دسته‌بندی والد -->
                        <div class="mb-3">
                            <label class="form-label">دسته‌بندی والد (اختیاری)</label>
                            <select name="parent_id" id="edit_parent_id" class="form-select">
                                <option value="">بدون والد</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="edit_parent_id_error"></div>
                        </div>

                        <!-- وضعیت -->
                        <div class="mb-3">
                            <label class="form-label">وضعیت <span class="text-danger">*</span></label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="">انتخاب وضعیت</option>
                                @foreach (\App\Models\Category::statuses() as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="edit_status_error"></div>
                        </div>

                        <!-- ترتیب نمایش -->
                        <div class="mb-3">
                            <label class="form-label">ترتیب نمایش</label>
                            <input type="number" name="sort_order" id="edit_sort_order" class="form-control"
                                placeholder="مثال: 0، 1، 2، ..." min="0">
                            <div class="invalid-feedback" id="edit_sort_order_error"></div>
                            <small class="text-muted">اعداد کوچکتر، بالاتر نمایش داده می‌شوند</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="button" class="btn btn-primary" id="saveEditBtn" onclick="updateCategory()">
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

        // تابع پر کردن مودال با اطلاعات دسته‌بندی
        function fillEditModal(element) {
            const id = element.dataset.id;
            const name = element.dataset.name;
            const status = element.dataset.status;
            const parentId = element.dataset.parentId || '';
            const sortOrder = element.dataset.sortOrder || 0;

            // پر کردن فیلدها
            document.getElementById('edit_category_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_status').value = status;
            document.getElementById('edit_parent_id').value = parentId;
            document.getElementById('edit_sort_order').value = sortOrder;

            // تنظیم اکشن فرم
            document.getElementById('editForm').action = `/admin/categories/${id}`;

            // حذف خطاهای قبلی
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.textContent = '';
            });
        }

        // تابع آپدیت دسته‌بندی
        function updateCategory() {
            const id = document.getElementById('edit_category_id').value;
            const name = document.getElementById('edit_name').value;
            const status = document.getElementById('edit_status').value;
            const parentId = document.getElementById('edit_parent_id').value;
            const sortOrder = document.getElementById('edit_sort_order').value;

            // نمایش اسپینر
            document.getElementById('saveText').classList.add('d-none');
            document.getElementById('saveSpinner').classList.remove('d-none');
            document.getElementById('saveEditBtn').disabled = true;

            // حذف خطاهای قبلی
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.textContent = '';
            });

            // ارسال درخواست Ajax
            $.ajax({
                url: '{{ route('admin.categories.update', ':id') }}'.replace(':id', id),
                type: 'PUT',
                data: {
                    _method: 'PUT',
                    _token: '{{ csrf_token() }}',
                    name: name,
                    status: status,
                    parent_id: parentId,
                    sort_order: sortOrder
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

                        if (errors.name) {
                            document.getElementById('edit_name').classList.add('is-invalid');
                            document.getElementById('edit_name_error').textContent = errors.name[0];
                        }

                        if (errors.status) {
                            document.getElementById('edit_status').classList.add('is-invalid');
                            document.getElementById('edit_status_error').textContent = errors.status[0];
                        }

                        if (errors.parent_id) {
                            document.getElementById('edit_parent_id').classList.add('is-invalid');
                            document.getElementById('edit_parent_id_error').textContent = errors.parent_id[0];
                        }

                        if (errors.sort_order) {
                            document.getElementById('edit_sort_order').classList.add('is-invalid');
                            document.getElementById('edit_sort_order_error').textContent = errors.sort_order[0];
                        }
                    } else {
                        // خطای عمومی
                        Swal.fire({
                            icon: 'error',
                            title: 'خطا!',
                            text: xhr.responseJSON?.message || 'خطا در ویرایش دسته‌بندی',
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
                    updateCategory();
                }
            }
        });
    </script>
@endpush
