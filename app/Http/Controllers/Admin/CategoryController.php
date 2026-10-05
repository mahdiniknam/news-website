<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(10);
        return view('admin.pages.category.index', compact('categories'));
    }


    public function store(StoreCategoryRequest $request)
    {
        try {
            // داده‌های تایید شده
            $validatedData = $request->validated();

            // ایجاد دسته‌بندی
            $category = Category::create($validatedData);

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'دسته‌بندی با موفقیت ایجاد شد.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'خطا در ایجاد دسته‌بندی: ' . $e->getMessage());
        }
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            // داده‌های تایید شده
            $validatedData = $request->validated();

            // به‌روزرسانی دسته‌بندی
            $category->update($validatedData);

            // اگر درخواست Ajax باشد
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'دسته‌بندی با موفقیت ویرایش شد.',
                    'category' => $category
                ]);
            }

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'دسته‌بندی با موفقیت ویرایش شد.');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطا در ویرایش دسته‌بندی: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'خطا در ویرایش دسته‌بندی: ' . $e->getMessage());
        }
    }

    public function show($slug, \Illuminate\Http\Request $request)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $posts = \App\Models\Post::with(['author', 'category'])
            ->where('status', 'published')
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::where('status', 1)
            ->withCount(['posts' => fn ($q) => $q->where('status', 'published')])
            ->ordered()
            ->get();

        return view('home.category', compact('category', 'posts', 'categories'));
    }

    public function destroy(Category $category)
    {
        try {
            // بررسی وجود زیردسته‌ها
            if ($category->children()->exists()) {
                return redirect()->back()
                    ->with('error', 'این دسته‌بندی دارای زیردسته است و قابل حذف نمی‌باشد.');
            }

            $category->delete();

            return redirect()->back()
                ->with('success', 'دسته‌بندی با موفقیت حذف شد.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'خطا در حذف دسته‌بندی: ' . $e->getMessage());
        }
    }
}
