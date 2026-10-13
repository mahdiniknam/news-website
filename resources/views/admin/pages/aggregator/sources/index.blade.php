@extends('admin.layout.master')
@section('admin-title','منابع — تجمیع اخبار')
@section('admin-content')
<div class="container-xxl flex-grow-1 container-p-y">
@if(session('success'))<div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<div class="card mb-4">
<h5 class="card-header">افزودن منبع جدید — فقط لینک کانال را وارد کنید</h5>
<div class="card-body">
<form action="{{ route('admin.aggregator.sources.store') }}" method="POST">@csrf
<div class="row">
<div class="col-md-6 mb-3">
<label class="form-label">لینک کانال *</label>
<input type="text" name="channel_link" class="form-control" required placeholder="https://t.me/varzesh3 یا @varzesh3" value="{{ old('channel_link') }}">
<small class="text-muted">لینک کامل کانال تلگرام/بله/ایتا/روبیکا یا نام کاربری</small>
</div>
<div class="col-md-3 mb-3">
<label class="form-label">هر چند دقیقه بررسی شود؟</label>
<input type="number" name="fetch_interval_minutes" class="form-control" min="1" max="1440" value="{{ old('fetch_interval_minutes', 10) }}">
</div>
<div class="col-md-3 mb-3 d-flex align-items-end">
<div class="form-check form-switch">
<input type="hidden" name="is_active" value="0">
<input class="form-check-input" type="checkbox" name="is_active" value="1" id="src_active" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
<label class="form-check-label" for="src_active">فعال</label>
</div>
</div>
<div class="col-12 mb-3">
<label class="form-label">کلمات فیلتر تبلیغات <span class="text-muted">(اختیاری)</span></label>
<textarea name="filter_keywords" class="form-control" rows="2" placeholder="هر کلمه در یک خط یا با کاما جدا کنید — مثلا: تبلیغات، فروش ویژه، تخفیف">{{ old('filter_keywords') }}</textarea>
<small class="text-muted">هر پستی که شامل یکی از این کلمات باشد خودکار رد و در لاگ «تبلیغ» ثبت می‌شود.</small>
</div>
<div class="col-md-6 mb-3">
<div class="form-check">
<input type="hidden" name="remove_links" value="0">
<input class="form-check-input" type="checkbox" name="remove_links" value="1" id="rm_links" {{ old('remove_links', '1') == '1' ? 'checked' : '' }}>
<label class="form-check-label" for="rm_links">حذف لینک‌ها از متن خبر</label>
</div>
<div class="form-check mt-2">
<input type="hidden" name="remove_tags" value="0">
<input class="form-check-input" type="checkbox" name="remove_tags" value="1" id="rm_tags" {{ old('remove_tags', '1') == '1' ? 'checked' : '' }}>
<label class="form-check-label" for="rm_tags">حذف @منشن‌ها و #هشتگ‌ها از متن خبر</label>
</div>
</div>
<div class="col-12"><button type="submit" class="btn btn-primary"><i class="bx bx-plus me-1"></i>افزودن منبع</button></div>
</div>
</form>
</div>
</div>

<div class="card">
<h5 class="card-header">لیست منابع</h5>
<div class="table-responsive text-nowrap">
<table class="table table-hover">
<thead><tr><th>#</th><th>لینک کانال</th><th>هر چند دقیقه</th><th>فیلتر</th><th>حذف لینک/تگ</th><th>وضعیت</th><th>آخرین دریافت</th><th>عملیات</th></tr></thead>
<tbody>
@forelse($sources as $s)
<tr>
<td>{{ $s->id }}</td>
<td><code dir="ltr">{{ $s->channel_link }}</code></td>
<td>{{ $s->fetch_interval_minutes }} دقیقه</td>
<td style="max-width:200px;white-space:normal;">{{ Str::limit($s->filter_keywords ?? '—', 60) }}</td>
<td>
@if($s->remove_links)<span class="badge bg-label-warning">لینک</span>@endif
@if($s->remove_tags)<span class="badge bg-label-info">#تگ</span>@endif
@if(!$s->remove_links && !$s->remove_tags)<span class="text-muted">—</span>@endif
</td>
<td>{!! $s->is_active ? '<span class="badge bg-success">فعال</span>' : '<span class="badge bg-secondary">غیرفعال</span>' !!}</td>
<td>{{ $s->last_fetched_at ? verta($s->last_fetched_at)->format('Y/m/d H:i') : '—' }}</td>
<td>
<div class="d-flex gap-1">
<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editSource{{ $s->id }}"><i class="bx bx-edit"></i></button>
<form action="{{ route('admin.aggregator.sources.destroy',$s) }}" method="POST" onsubmit="return confirm('حذف شود؟')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button></form>
</div>
</td>
</tr>

<div class="modal fade" id="editSource{{ $s->id }}" tabindex="-1" aria-hidden="true">
<div class="modal-dialog"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">ویرایش منبع #{{ $s->id }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form action="{{ route('admin.aggregator.sources.update',$s) }}" method="POST">@csrf @method('PUT')
<div class="modal-body">
<div class="mb-3"><label class="form-label">لینک کانال *</label><input type="text" name="channel_link" class="form-control" required value="{{ $s->channel_link }}"></div>
<div class="mb-3"><label class="form-label">هر چند دقیقه</label><input type="number" name="fetch_interval_minutes" class="form-control" value="{{ $s->fetch_interval_minutes }}"></div>
<div class="mb-3"><label class="form-label">کلمات فیلتر</label><textarea name="filter_keywords" class="form-control" rows="2">{{ $s->filter_keywords }}</textarea></div>
<div class="form-check"><input type="hidden" name="remove_links" value="0"><input class="form-check-input" type="checkbox" name="remove_links" value="1" {{ $s->remove_links ? 'checked' : '' }} id="rm_l_{{ $s->id }}"><label class="form-check-label" for="rm_l_{{ $s->id }}">حذف لینک‌ها</label></div>
<div class="form-check"><input type="hidden" name="remove_tags" value="0"><input class="form-check-input" type="checkbox" name="remove_tags" value="1" {{ $s->remove_tags ? 'checked' : '' }} id="rm_t_{{ $s->id }}"><label class="form-check-label" for="rm_t_{{ $s->id }}">حذف تگ‌ها</label></div>
<div class="form-check mt-2"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $s->is_active ? 'checked' : '' }} id="act_{{ $s->id }}"><label class="form-check-label" for="act_{{ $s->id }}">فعال</label></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button><button type="submit" class="btn btn-primary">ذخیره</button></div>
</form>
</div></div>
</div>

@empty<tr><td colspan="8" class="text-center py-4 text-muted">هنوز منبعی ثبت نشده — لینک یک کانال را بالا وارد کنید.</td></tr>@endforelse
</tbody>
</table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $sources->links('pagination::bootstrap-5') }}</div>
</div>
</div>
@endsection
