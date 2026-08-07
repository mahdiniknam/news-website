<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Http\Requests\Author\StorePostRequest;
use App\Http\Requests\Author\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * نمایش لیست اخبار نویسنده
     */
    public function index()
    {
        $user = Auth::guard('admin')->user();

        // نویسنده فقط اخبار خود را می‌بیند
        $posts = Post::with(['author', 'category'])
            ->where('author_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('author.pages.post.index', compact('posts'));
    }

    /**
     * نمایش فرم ایجاد خبر
     */
    public function create()
    {
        $categories = Category::where('status', 1)->ordered()->get();
        $tags = Tag::where('status', 'active')->get();

        return view('author.pages.post.create', compact('categories', 'tags'));
    }

    /**
     * ذخیره خبر جدید
     */
    public function store(StorePostRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // مدیریت تصویر شاخص
            if ($request->hasFile('featured_image')) {
                $imagePath = $request->file('featured_image')->store('posts', 'public');
                $validatedData['featured_image'] = $imagePath;
            }

            // تنظیم نویسنده
            $validatedData['author_id'] = Auth::guard('admin')->id();

            // وضعیت همیشه pending (در انتظار تایید)
            $validatedData['status'] = 'pending';

            // ایجاد خبر
            $post = Post::create($validatedData);

            // مدیریت تگ‌ها (اگر تگ‌ها وجود داشته باشند)
            if ($request->has('tags') && !empty($request->tags)) {
                $post->tags()->sync($request->tags);
            }

            return redirect()
                ->route('author.posts.index')
                ->with('success', 'خبر با موفقیت ایجاد شد و در انتظار تایید مدیر است.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'خطا در ایجاد خبر: ' . $e->getMessage());
        }
    }

    /**
     * نمایش فرم ویرایش خبر
     */
    public function edit(Post $post)
    {
        $user = Auth::guard('admin')->user();

        // بررسی دسترسی: نویسنده فقط می‌تواند خبرهای خود را ویرایش کند
        if ($post->author_id != $user->id) {
            abort(403, 'شما دسترسی به ویرایش این خبر ندارید.');
        }

        // فقط خبرهای با وضعیت draft, pending, rejected قابل ویرایش هستند
        if (!in_array($post->status, ['draft', 'pending', 'rejected'])) {
            abort(403, 'این خبر قابل ویرایش نیست.');
        }

        $categories = Category::where('status', 1)->ordered()->get();
        $tags = Tag::where('status', 'active')->get();

        return view('author.pages.post.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * به‌روزرسانی خبر
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        try {
            $user = Auth::guard('admin')->user();

            // بررسی دسترسی
            if ($post->author_id != $user->id) {
                abort(403, 'شما دسترسی به ویرایش این خبر ندارید.');
            }

            // فقط خبرهای با وضعیت draft, pending, rejected قابل ویرایش هستند
            if (!in_array($post->status, ['draft', 'pending', 'rejected'])) {
                abort(403, 'این خبر قابل ویرایش نیست.');
            }

            $validatedData = $request->validated();

            // مدیریت تصویر شاخص
            if ($request->hasFile('featured_image')) {
                if ($post->featured_image && Storage::disk('public')->exists($post->featured_image)) {
                    Storage::disk('public')->delete($post->featured_image);
                }
                $imagePath = $request->file('featured_image')->store('posts', 'public');
                $validatedData['featured_image'] = $imagePath;
            }

            // اگر خبر در وضعیت rejected است و دوباره ویرایش می‌شود، به pending برگردد
            if ($post->status === 'rejected') {
                $validatedData['status'] = 'pending';
                $validatedData['rejection_reason'] = null;
            }

            // به‌روزرسانی خبر
            $post->update($validatedData);

            // مدیریت تگ‌ها
            if ($request->has('tags')) {
                $post->tags()->sync($request->tags);
            }

            return redirect()
                ->route('author.posts.index')
                ->with('success', 'خبر با موفقیت ویرایش شد و مجدداً در انتظار تایید است.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'خطا در ویرایش خبر: ' . $e->getMessage());
        }
    }

    /**
     * حذف خبر
     */
    public function destroy(Post $post)
    {
        try {
            $user = Auth::guard('admin')->user();

            // بررسی دسترسی
            if ($post->author_id != $user->id) {
                abort(403, 'شما دسترسی به حذف این خبر ندارید.');
            }

            // فقط خبرهای با وضعیت draft, pending, rejected قابل حذف هستند
            if (!in_array($post->status, ['draft', 'pending', 'rejected'])) {
                abort(403, 'این خبر قابل حذف نیست.');
            }

            // حذف تصویر
            if ($post->featured_image && Storage::disk('public')->exists($post->featured_image)) {
                Storage::disk('public')->delete($post->featured_image);
            }

            $post->delete();

            return redirect()
                ->route('author.posts.index')
                ->with('success', 'خبر با موفقیت حذف شد.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'خطا در حذف خبر: ' . $e->getMessage());
        }
    }

    /**
     * نمایش خبر (برای مشاهده)
     */
    public function show(Post $post)
    {
        $user = Auth::guard('admin')->user();

        // بررسی دسترسی
        if ($post->author_id != $user->id) {
            abort(403, 'شما دسترسی به مشاهده این خبر ندارید.');
        }

        return view('author.pages.post.show', compact('post'));
    }


    public function showRejectionReason(Post $post)
    {
        if ($post->status !== 'rejected' || empty($post->rejection_reason)) {
            return response()->json([
                'message' => 'دلیلی برای رد وجود ندارد'
            ], 404);
        }

        // دریافت نام تایید کننده (اگر وجود داشته باشد)
        $approver = null;
        if ($post->approved_by) {
            $approver = \App\Models\Admin::find($post->approved_by);
        }

        return response()->json([
            'reason' => $post->rejection_reason,
            'rejected_at' => $post->updated_at ? verta($post->updated_at)->format('Y/m/d H:i') : '-',
            'approver' => $approver ? $approver->name : 'نامشخص'
        ]);
    }
}
