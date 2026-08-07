<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // اسلایدر: اخبار ویژه (special = true)
        $slides = Post::with('author', 'category')
            ->where('is_featured', true)
            ->where('type', 'news')
            ->where('status', 'published')
            ->latest('published_at')
            ->limit(5)
            ->get();

        // اخبار اصلی
        $news = Post::with('author', 'category')
            ->where('status', 'published')
            ->where('type', 'news')
            ->latest('published_at')
            ->paginate(6);

        // یادداشت‌ها (با دسته‌بندی خاص یا اخبار کوتاه)
        $notes = Post::with('author')
            ->where('status', 'published')
            ->where('type', 'note') // فرض کنید نوع یادداشت‌ها "note" است
            ->latest('published_at')
            ->limit(3)
            ->get();

        // مصاحبه‌ها
        $interviews = Post::with('author', 'category')
            ->where('status', 'published')
            ->where('type', 'interview') // فرض کنید نوع مصاحبه‌ها "interview" است   
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('home.index', compact('slides', 'news', 'notes', 'interviews'));
    }

    public function show($slug)
    {
        // دریافت پست با اسلاگ و وضعیت منتشر شده
        $post = Post::with(['author', 'category'])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // افزایش بازدید
        $post->increment('view_count');

        // محاسبه زمان مطالعه
        $readingTime = $post->reading_time;

        // دریافت اخبار مرتبط (همان دسته‌بندی، به جز خود خبر)
        $relatedPosts = Post::where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->where('status', 'published')
            ->latest('published_at')
            ->limit(4)
            ->get();

        // دریافت دسته‌بندی‌ها برای منو و سایدبار
        $categories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->with('children')
            ->ordered()
            ->get();

        // پربازدیدترین اخبار برای سایدبار
        $popularPosts = Post::where('status', 'published')
            ->latest('view_count')
            ->limit(5)
            ->get();

        // تنظیم متا تگ‌ها
        $metaTitle = $post->meta_title ?? $post->title;
        $metaDescription = $post->meta_description ?? $post->excerpt;

        return view('home.showNews', compact(
            'post',
            'readingTime',
            'relatedPosts',
            'categories',
            'popularPosts',
            'metaTitle',
            'metaDescription'
        ));
    }

    public function allNews(Request $request)
    {
        $query = Post::with(['author', 'category'])
            ->where('status', 'published')
            ->where('type', 'news') // فرض کنید نوع اخبار "news" است
            ->latest('published_at');

        // فیلتر بر اساس دسته‌بندی
        if ($request->has('category') && $request->category) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // جستجو
        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }

        $posts = $query->paginate(12);

        // دریافت دسته‌بندی‌ها برای فیلتر
        $categories = Category::where('status', 1)->ordered()->get();

        return view('home.allNews', compact('posts', 'categories'));
    }


    /**
     * جستجو در اخبار
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return redirect()->route('news.index');
        }

        $posts = Post::with(['author', 'category'])
            ->where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('content', 'LIKE', "%{$query}%")
                    ->orWhereHas('author', function ($author) use ($query) {
                        $author->where('name', 'LIKE', "%{$query}%");
                    });
            })
            ->latest('published_at')
            ->paginate(12);

        $categories = Category::where('status', 1)
            ->whereNull('parent_id')
            ->with('children')
            ->ordered()
            ->get();

        return view('home.searchResult', compact('posts', 'query', 'categories'));
    }


    /**
     * نمایش همه یادداشت‌ها
     */
    public function allNotes(Request $request)
    {
        $category = Category::where('slug', 'yaddasht')
            ->orWhere('name', 'یادداشت')
            ->first();

        $query = Post::with(['author', 'category'])
            ->where('status', 'published')
            ->where('type', 'note') // فرض کنید نوع یادداشت‌ها "note" است
            ->latest('published_at');

        // جستجو
        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }

        $posts = $query->paginate(12);

        // دریافت دسته‌بندی‌ها
        $categories = Category::where('status', 1)->ordered()->get();

        return view('home.allNotes', compact('posts', 'categories'));
    }

    /**
     * نمایش همه مصاحبه‌ها
     */
    public function allInterviews(Request $request)
    {
        $category = Category::where('slug', 'mosabehe')
            ->orWhere('name', 'مصاحبه')
            ->first();

        $query = Post::with(['author', 'category'])
            ->where('status', 'published')
            ->where('type','interview') // فرض کنید نوع مصاحبه‌ها "interview" است
            ->latest('published_at');

        // جستجو
        if ($request->has('q') && $request->q) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }

        $posts = $query->paginate(12);

        // دریافت دسته‌بندی‌ها
        $categories = Category::where('status', 1)->ordered()->get();

        return view('home.allInterviews', compact('posts', 'categories'));
    }
}
