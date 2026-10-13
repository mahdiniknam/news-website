@extends('admin.layout.master')
@section('admin-title','ربات‌های انتشار — تجمیع اخبار')
@section('admin-content')
<div class="container-xxl flex-grow-1 container-p-y">
@if(session('success'))<div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

<div class="alert alert-info">
<strong>راهنما:</strong> یک ربات در @BotFather بسازید، توکن را اینجا وارد کنید و سپس ربات را در کانال‌های خود عضو و «ادمین» کنید تا بتواند خبرهای تجمیع‌شده را منتشر کند.
</div>

<div class="card mb-4">
<h5 class="card-header">افزودن ربات انتشار</h5>
<div class="card-body">
<form action="{{ route('admin.aggregator.destinations.store') }}" method="POST">@csrf
<div class="row">
<div class="col-md-4 mb-3"><label class="form-label">نام ربات/مقصد *</label><input type="text" name="name" class="form-control" required placeholder="مثلا: ربات اصلی تلگرام" value="{{ old('name') }}"></div>
<div class="col-md-2 mb-3"><label class="form-label">پلتفرم *</label><select name="platform" class="form-select" required><option value="telegram" {{ old('platform')=='telegram'?'selected':'' }}>تلگرام</option><option value="bale" {{ old('platform')=='bale'?'selected':'' }}>بله</option></select></div>
<div class="col-md-6 mb-3"><label class="form-label">توکن ربات *</label><input type="text" name="bot_token" class="form-control text-start" dir="ltr" required placeholder="123456:ABC-DEF..."><small class="text-muted">از @BotFather بگیرید — رمزنگاری‌شده ذخیره می‌شود.</small></div>
<div class="col-md-6 mb-3"><label class="form-label">شناسه کانال مقصد *</label><input type="text" name="channel_identifier" class="form-control text-start" dir="ltr" required placeholder="@my_news_channel یا -100123456789" value="{{ old('channel_identifier') }}"></div>
<div class="col-md-3 mb-3 d-flex align-items-end"><div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="dst_active" {{ old('is_active','1')=='1'?'checked':'' }}><label class="form-check-label" for="dst_active">فعال</label></div></div>
<div class="col-12"><button type="submit" class="btn btn-primary"><i class="bx bx-plus me-1"></i>افزودن ربات</button></div>
</div>
</form>
</div>
</div>

<div class="card">
<h5 class="card-header">لیست ربات‌های انتشار</h5>
<div class="table-responsive text-nowrap">
<table class="table table-hover">
<thead><tr><th>#</th><th>نام</th><th>پلتفرم</th><th>کانال مقصد</th><th>وضعیت</th><th>عملیات</th></tr></thead>
<tbody>
@forelse($destinations as $d)
<tr>
<td>{{ $d->id }}</td>
<td>{{ $d->name }}</td>
<td><span class="badge bg-label-info">{{ $d->platform === 'telegram' ? 'تلگرام' : 'بله' }}</span></td>
<td><code dir="ltr">{{ $d->channel_identifier }}</code></td>
<td>{!! $d->is_active ? '<span class="badge bg-success">فعال</span>' : '<span class="badge bg-secondary">غیرفعال</span>' !!}</td>
<td>
<div class="d-flex gap-1">
<button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editDest{{ $d->id }}"><i class="bx bx-edit"></i></button>
<form action="{{ route('admin.aggregator.destinations.destroy',$d) }}" method="POST" onsubmit="return confirm('حذف شود؟')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bx bx-trash"></i></button></form>
</div>
</td>
</tr>
<div class="modal fade" id="editDest{{ $d->id }}" tabindex="-1" aria-hidden="true">
<div class="modal-dialog"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">ویرایش مقصد #{{ $d->id }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form action="{{ route('admin.aggregator.destinations.update',$d) }}" method="POST">@csrf @method('PUT')
<div class="modal-body">
<div class="mb-3"><label class="form-label">نام *</label><input type="text" name="name" class="form-control" required value="{{ $d->name }}"></div>
<div class="mb-3"><label class="form-label">پلتفرم *</label><select name="platform" class="form-select" required><option value="telegram" {{ $d->platform=='telegram'?'selected':'' }}>تلگرام</option><option value="bale" {{ $d->platform=='bale'?'selected':'' }}>بله</option></select></div>
<div class="mb-3"><label class="form-label">توکن جدید (خالی = بدون تغییر)</label><input type="text" name="bot_token" class="form-control text-start" dir="ltr" placeholder="—"></div>
<div class="mb-3"><label class="form-label">شناسه کانال مقصد *</label><input type="text" name="channel_identifier" class="form-control text-start" dir="ltr" required value="{{ $d->channel_identifier }}"></div>
<div class="form-check"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $d->is_active?'checked':'' }} id="d_act_{{ $d->id }}"><label class="form-check-label" for="d_act_{{ $d->id }}">فعال</label></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button><button type="submit" class="btn btn-primary">ذخیره</button></div>
</form>
</div></div>
</div>
@empty<tr><td colspan="6" class="text-center py-4 text-muted">هنوز رباتی ثبت نشده.</td></tr>@endforelse
</tbody>
</table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $destinations->links('pagination::bootstrap-5') }}</div>
</div>
</div>
@endsection
