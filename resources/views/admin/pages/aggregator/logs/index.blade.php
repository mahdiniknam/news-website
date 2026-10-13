@extends('admin.layout.master')
@section('admin-title','لاگ‌ها — تجمیع اخبار')
@section('admin-content')
<div class="container-xxl flex-grow-1 container-p-y">
<div class="d-flex justify-content-between align-items-center mb-3">
<h4 class="mb-0">لاگ تجمیع اخبار</h4>
<form method="GET" class="d-flex gap-2">
<select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
<option value="">همه وضعیت‌ها</option>
<option value="published" {{ request('status')=='published'?'selected':'' }}>منتشرشده</option>
<option value="skipped" {{ request('status')=='skipped'?'selected':'' }}>ردشده/تبلیغ</option>
<option value="processing" {{ request('status')=='processing'?'selected':'' }}>در حال پردازش</option>
<option value="failed" {{ request('status')=='failed'?'selected':'' }}>خطا</option>
</select>
@if(request('status'))<a href="{{ route('admin.aggregator.logs.index') }}" class="btn btn-sm btn-outline-secondary">حذف فیلتر</a>@endif
</form>
</div>

<div class="card">
<div class="table-responsive text-nowrap">
<table class="table table-hover">
<thead><tr><th>#</th><th>منبع</th><th>شناسه پیام</th><th>وضعیت</th><th>دلیل/توضیح</th><th>خبر سایت</th><th>تاریخ</th></tr></thead>
<tbody>
@forelse($logs as $log)
<tr>
<td>{{ $log->id }}</td>
<td><code dir="ltr" class="small">{{ $log->sourceChannel->channel_link ?? '—' }}</code></td>
<td class="small">{{ $log->external_message_id }}</td>
<td>
@if($log->status==='published')<span class="badge bg-success">منتشرشده</span>
@elseif($log->status==='skipped')<span class="badge bg-warning">رد/تبلیغ</span>
@elseif($log->status==='failed')<span class="badge bg-danger">خطا</span>
@else<span class="badge bg-secondary">{{ $log->status }}</span>@endif
</td>
<td style="max-width:280px;white-space:normal;" class="small">{{ $log->status_reason ?? '—' }}</td>
<td>
@if($log->post)<a href="{{ route('post.show', $log->post->slug) }}" target="_blank" class="small">{{ Str::limit($log->post->title, 30) }}</a>@else<span class="text-muted small">—</span>@endif
</td>
<td class="small">{{ verta($log->created_at)->format('Y/m/d H:i') }}</td>
</tr>
@empty<tr><td colspan="7" class="text-center py-4 text-muted">لاگی یافت نشد.</td></tr>@endforelse
</tbody>
</table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $logs->links('pagination::bootstrap-5') }}</div>
</div>
</div>
@endsection
