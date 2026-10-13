<?php

namespace App\Http\Controllers\Admin\Aggregator;

use App\Http\Controllers\Controller;
use App\Models\SourceChannel;
use Illuminate\Http\Request;

class SourceChannelController extends Controller
{
    public function index()
    {
        $sources = SourceChannel::latest()->paginate(15);
        return view('admin.pages.aggregator.sources.index', compact('sources'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'channel_link' => 'required|string|max:255',
            'fetch_interval_minutes' => 'nullable|integer|min:1|max:1440',
            'filter_keywords' => 'nullable|string',
            'remove_links' => 'nullable|boolean',
            'remove_tags' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        SourceChannel::create([
            'name' => trim($data['channel_link']),
            'platform' => 'telegram',
            'channel_link' => trim($data['channel_link']),
            'fetch_interval_minutes' => $data['fetch_interval_minutes'] ?? 10,
            'filter_keywords' => $data['filter_keywords'] ?? null,
            'remove_links' => $request->boolean('remove_links', true),
            'remove_tags' => $request->boolean('remove_tags', true),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'منبع با موفقیت اضافه شد.');
    }

    public function update(Request $request, SourceChannel $source)
    {
        $data = $request->validate([
            'channel_link' => 'required|string|max:255',
            'fetch_interval_minutes' => 'nullable|integer|min:1|max:1440',
            'filter_keywords' => 'nullable|string',
            'remove_links' => 'nullable|boolean',
            'remove_tags' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $source->update([
            'name' => trim($data['channel_link']),
            'channel_link' => trim($data['channel_link']),
            'fetch_interval_minutes' => $data['fetch_interval_minutes'] ?? $source->fetch_interval_minutes,
            'filter_keywords' => $data['filter_keywords'] ?? null,
            'remove_links' => $request->boolean('remove_links'),
            'remove_tags' => $request->boolean('remove_tags'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'منبع به‌روزرسانی شد.');
    }

    public function destroy(SourceChannel $source)
    {
        $source->delete();
        return back()->with('success', 'منبع حذف شد.');
    }
}
