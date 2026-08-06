<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::paginate(10);
        return view('admin.pages.tag.index', compact('tags'));
    }

    public function store(Request $request)
    {

        $valideted = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,in_active',
        ]);

        Tag::create([
            'name' => $valideted['name'],
            'status' => $valideted['status'],
        ]);

        return redirect()->back()->with('success', 'تگ با موفقیت ایجاد شد.');
    }

    public function update(Request $request, Tag $tag)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
            'status' => 'required|in:active,in_active',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tag->name = $request->name;
            $tag->status = $request->status;

            // اگر عنوان تغییر کرده، اسلاگ را مجدداً تولید کن
            if ($tag->isDirty('name')) {
                $tag->slug = $tag->generateSlug($request->name);
            }

            $tag->save();

            return response()->json([
                'success' => true,
                'message' => 'تگ با موفقیت ویرایش شد.',
                'tag' => $tag
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ویرایش تگ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();
        return redirect()->back()->with('success', 'تگ با موفقیت حذف شد.');
    }
}
