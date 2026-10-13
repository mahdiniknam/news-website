<?php

namespace App\Http\Controllers\Admin\Aggregator;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Http\Request;

class AggregatorSettingsController extends Controller
{
    public function index()
    {
        $selectedAuthorId = Setting::get('aggregator_default_author_id');
        $selectedCategoryId = Setting::get('aggregator_default_category_id');
        $selectedTagIds = json_decode(Setting::get('aggregator_default_tag_ids', '[]'), true) ?: [];
        $selectedMaxVideoMb = (int) (Setting::get('aggregator_max_video_mb') ?? 50);

        $admins = Admin::orderBy('name')->get(['id', 'name']);
        $categories = Category::orderBy('name')->get(['id', 'name']);
        $tags = Tag::orderBy('name')->get(['id', 'name']);

        return view('admin.pages.aggregator.settings', compact('admins', 'categories', 'tags', 'selectedAuthorId', 'selectedCategoryId', 'selectedTagIds', 'selectedMaxVideoMb'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'default_author_id' => 'required|exists:admins,id',
            'default_category_id' => 'required|exists:categories,id',
            'default_tag_ids' => 'nullable|array',
            'default_tag_ids.*' => 'exists:tags,id',
            'max_video_mb' => 'required|integer|min:5|max:500',
        ]);

        Setting::set('aggregator_default_author_id', (string)$data['default_author_id']);
        Setting::set('aggregator_default_category_id', (string)$data['default_category_id']);
        Setting::set('aggregator_default_tag_ids', json_encode($data['default_tag_ids'] ?? []));
        Setting::set('aggregator_max_video_mb', (string)$data['max_video_mb']);

        return back()->with('success', 'تنظیمات ذخیره شد.');
    }
}
