@extends('admin.layout.master')
@section('admin-title','تنظیمات انتشار در سایت — تجمیع اخبار')
@section('admin-content')
<div class="container-xxl flex-grow-1 container-p-y">
@if(session('success'))<div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<div class="card">
<h5 class="card-header">تنظیمات انتشار خودکار در سایت</h5>
<div class="card-body">
<p class="text-muted">اخبار تجمیع‌شده با این تنظیمات در سایت منتشر می‌شوند (نویسنده، دسته‌بندی و تگ‌ها).</p>
<form action="{{ route('admin.aggregator.settings.store') }}" method="POST">@csrf
<div class="row">
<div class="col-md-4 mb-3">
<label class="form-label">نویسنده/ادمین پیش‌فرض *</label>
<select name="default_author_id" class="form-select" required>
<option value="">— انتخاب کنید —</option>
@foreach($admins as $a)<option value="{{ $a->id }}" {{ (string)$selectedAuthorId === (string)$a->id ? 'selected' : '' }}>{{ $a->name }}</option>@endforeach
</select>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">دسته‌بندی پیش‌فرض *</label>
<select name="default_category_id" class="form-select" required>
<option value="">— انتخاب کنید —</option>
@foreach($categories as $c)<option value="{{ $c->id }}" {{ (string)$selectedCategoryId === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
</select>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">تگ‌ها (اختیاری)</label>
<select name="default_tag_ids[]" class="form-select" multiple size="6">
@foreach($tags as $t)<option value="{{ $t->id }}" {{ in_array($t->id, $selectedTagIds) ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach
</select>
<small class="text-muted">با Ctrl/Cmd چند تگ انتخاب کنید.</small>
</div>
<div class="col-md-4 mb-3">
<label class="form-label">حداکثر حجم ویدیو (مگابایت) *</label>
<input type="number" name="max_video_mb" class="form-control" min="5" max="500" required value="{{ old('max_video_mb', $selectedMaxVideoMb) }}">
<small class="text-muted">ویدیوهای بزرگ‌تر از این حد دانلود نمی‌شوند (پیش‌فرض ۵۰). اگر خبر فقط ویدیو حجیم داشته باشد، بدون ویدیو منتشر می‌شود.</small>
</div>
<div class="col-12 mt-2"><button type="submit" class="btn btn-primary"><i class="bx bx-save me-1"></i>ذخیره تنظیمات</button></div>
</div>
</form>
</div>
</div>
</div>
@endsection
