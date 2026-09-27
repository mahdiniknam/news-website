<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\PostNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user();
        $isAdmin = $user && ($user->hasRole('super-admin') || $user->hasRole('admin'));

        if ($isAdmin) {
            // ادمین همه اخبار را می‌بیند
            $posts = Post::with(['author', 'category'])
                ->latest()
                ->paginate(10);
        } else {
            // نویسنده فقط اخبار خود را می‌بیند
            $posts = Post::with(['author', 'category'])
                ->where('author_id', $user->id)
                ->latest()
                ->paginate(3);
        }

        return view('admin.pages.post.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->ordered()->get();
        $tags = Tag::where('status', 'active')->get();
        $user = Auth::guard('admin')->user();
        $isAdmin = $user && ($user->hasRole('super-admin') || $user->hasRole('admin'));

        return view('admin.pages.post.create', compact('categories', 'isAdmin', 'tags'));
    }

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

            // بررسی نقش کاربر برای تعیین وضعیت خودکار
            $user = Auth::guard('admin')->user();
            if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
                $validatedData['status'] = 'published';
                $validatedData['published_at'] = now();
            } else {
                $validatedData['status'] = 'pending';
            }

            // ایجاد خبر
            $post = Post::create($validatedData);

            $message = $post->status === 'published'
                ? 'خبر با موفقیت ایجاد و منتشر شد.'
                : 'خبر با موفقیت ایجاد شد و در انتظار تایید مدیر است.';

            return redirect()
                ->route('admin.posts.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'خطا در ایجاد خبر: ' . $e->getMessage());
        }
    }

    public function edit(Post $post)
    {
        $user = Auth::guard('admin')->user();
        $isAdmin = $user && ($user->hasRole('super-admin') || $user->hasRole('admin'));

        // بررسی دسترسی
        if (!$isAdmin && $post->author_id != $user->id) {
            abort(403, 'شما دسترسی به ویرایش این خبر ندارید.');
        }

        $categories = Category::where('status', 1)->ordered()->get();
        return view('admin.pages.post.edit', compact('post', 'categories', 'isAdmin'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        try {
            // بررسی دسترسی
            $user = Auth::guard('admin')->user();
            $isAdmin = $user && ($user->hasRole('super-admin') || $user->hasRole('admin'));

            if (!$isAdmin && $post->author_id != $user->id) {
                abort(403, 'شما دسترسی به ویرایش این خبر ندارید.');
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

            return redirect()
                ->route('admin.posts.index')
                ->with('success', 'خبر با موفقیت ویرایش شد.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'خطا در ویرایش خبر: ' . $e->getMessage());
        }
    }

    public function destroy(Post $post)
    {
        try {
            // بررسی دسترسی
            $user = Auth::guard('admin')->user();
            $isAdmin = $user && ($user->hasRole('super-admin') || $user->hasRole('admin'));

            if (!$isAdmin && $post->author_id != $user->id) {
                abort(403, 'شما دسترسی به حذف این خبر ندارید.');
            }

            // حذف تصویر
            if ($post->featured_image && Storage::disk('public')->exists($post->featured_image)) {
                Storage::disk('public')->delete($post->featured_image);
            }

            $post->delete();

            return redirect()
                ->back()
                ->with('success', 'خبر با موفقیت حذف شد.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'خطا در حذف خبر: ' . $e->getMessage());
        }
    }

    // متدهای تایید و رد
    public function approve(Post $post, PostNotifier $notifier)
    {
        try {
            if ($post->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'این خبر در وضعیت قابل تایید نیست.'
                ], 422);
            }

            $post->approve(Auth::guard('admin')->id());

            // اطلاع‌رسانی تایید به خبرنگار از طریق ربات بله
            try {
                $notifier->notifyAuthorApproved($post);
            } catch (\Throwable $e) {
                report($e);
            }

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'خبر با موفقیت تایید شد.']);
            }

            return redirect()
                ->back()
                ->with('success', 'خبر با موفقیت تایید شد.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'خطا در تایید خبر'], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'خطا در تایید خبر: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Post $post, PostNotifier $notifier)
    {

        try {

        $admin=auth('admin')->user();
            $request->validate([
                'rejection_reason' => 'required|string|min:5|max:500',
            ]);

            // تغییر وضعیت به rejected
            $post->status = 'rejected';
            $post->rejection_reason = $request->rejection_reason;
            
            $post->save();

            // اطلاع‌رسانی رد + دلیل به خبرنگار از طریق ربات بله
            try {
                $notifier->notifyAuthorRejected($post);
            } catch (\Throwable $e) {
                report($e);
            }

            // اگر درخواست Ajax باشد
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'خبر با موفقیت رد شد.'
                ]);
            }

            return redirect()
                ->back()
                ->with('success', 'خبر با موفقیت رد شد.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطا در اعتبارسنجی',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطا در رد خبر: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'خطا در رد خبر: ' . $e->getMessage());
        }
    }


  
    public function publish(Post $post, PostNotifier $notifier)
    {
        try {
            if (in_array($post->status, ['rejected'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'خبر رد شده قابل انتشار نیست.'
                ], 422);
            }

            $post->publish();

            // اطلاع‌رسانی انتشار به خبرنگار از طریق ربات بله
            try {
                $notifier->notifyAuthorPublished($post);
            } catch (\Throwable $e) {
                report($e);
            }

            if (request()->ajax()) {
                return response()->json(['success' => true, 'message' => 'خبر با موفقیت منتشر شد.']);
            }

            return redirect()
                ->back()
                ->with('success', 'خبر با موفقیت منتشر شد.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'خطا در انتشار خبر'], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'خطا در انتشار خبر: ' . $e->getMessage());
        }
    }

    public function toggleSpecial(Post $post)
    {
        try {
            $post->special = !$post->special;
            $post->save();

            return redirect()
                ->back()
                ->with('success', $post->special ? 'خبر به عنوان اسلاید ویژه انتخاب شد.' : 'خبر از حالت اسلاید ویژه خارج شد.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'خطا در تغییر وضعیت ویژه: ' . $e->getMessage());
        }
    }
}
