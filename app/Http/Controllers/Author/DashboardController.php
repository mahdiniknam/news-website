<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function dashboard()
    {
        // دریافت کاربر لاگین شده
        $author = Auth::guard('admin')->user();
        $authorId = $author->id;

        // تعداد کل اخبار نویسنده
        $totalPosts = Post::where('author_id', $authorId)->count();

        // تعداد کل یادداشت‌های نویسنده
        $totalNotes = Post::where('author_id', $authorId)
            ->whereHas('category', function ($q) {
                $q->where('slug', 'yaddasht')->orWhere('name', 'یادداشت');
            })->count();

        // تعداد کل مصاحبه‌های نویسنده
        $totalInterviews = Post::where('author_id', $authorId)
            ->whereHas('category', function ($q) {
                $q->where('slug', 'mosabehe')->orWhere('name', 'مصاحبه');
            })->count();

        // وضعیت اخبار نویسنده
        $publishedPosts = Post::where('author_id', $authorId)
            ->where('status', 'published')->count();
        $pendingPosts = Post::where('author_id', $authorId)
            ->where('status', 'pending')->count();
        $draftPosts = Post::where('author_id', $authorId)
            ->where('status', 'draft')->count();
        $rejectedPosts = Post::where('author_id', $authorId)
            ->where('status', 'rejected')->count();

        // آمار امروز نویسنده
        $today = Carbon::today();
        $todayPosts = Post::where('author_id', $authorId)
            ->whereDate('created_at', $today)->count();
        $todayNotes = Post::where('author_id', $authorId)
            ->whereHas('category', function ($q) {
                $q->where('slug', 'yaddasht')->orWhere('name', 'یادداشت');
            })->whereDate('created_at', $today)->count();
        $todayInterviews = Post::where('author_id', $authorId)
            ->whereHas('category', function ($q) {
                $q->where('slug', 'mosabehe')->orWhere('name', 'مصاحبه');
            })->whereDate('created_at', $today)->count();

        // آخرین اخبار نویسنده
        $latestPosts = Post::with(['author', 'category'])
            ->where('author_id', $authorId)
            ->latest('created_at')
            ->limit(5)
            ->get();

        // درصد رشد اخبار نویسنده
        $lastMonth = Carbon::now()->subMonth();

        $currentMonthPosts = Post::where('author_id', $authorId)
            ->whereMonth('created_at', Carbon::now()->month)->count();
        $lastMonthPosts = Post::where('author_id', $authorId)
            ->whereMonth('created_at', $lastMonth->month)->count();
        $postsGrowth = $lastMonthPosts > 0 ? round((($currentMonthPosts - $lastMonthPosts) / $lastMonthPosts) * 100) : 0;

        // رشد یادداشت‌های نویسنده
        $currentMonthNotes = Post::where('author_id', $authorId)
            ->whereHas('category', function ($q) {
                $q->where('slug', 'yaddasht')->orWhere('name', 'یادداشت');
            })->whereMonth('created_at', Carbon::now()->month)->count();
        $lastMonthNotes = Post::where('author_id', $authorId)
            ->whereHas('category', function ($q) {
                $q->where('slug', 'yaddasht')->orWhere('name', 'یادداشت');
            })->whereMonth('created_at', $lastMonth->month)->count();
        $notesGrowth = $lastMonthNotes > 0 ? round((($currentMonthNotes - $lastMonthNotes) / $lastMonthNotes) * 100) : 0;

        // رشد مصاحبه‌های نویسنده
        $currentMonthInterviews = Post::where('author_id', $authorId)
            ->whereHas('category', function ($q) {
                $q->where('slug', 'mosabehe')->orWhere('name', 'مصاحبه');
            })->whereMonth('created_at', Carbon::now()->month)->count();
        $lastMonthInterviews = Post::where('author_id', $authorId)
            ->whereHas('category', function ($q) {
                $q->where('slug', 'mosabehe')->orWhere('name', 'مصاحبه');
            })->whereMonth('created_at', $lastMonth->month)->count();
        $interviewsGrowth = $lastMonthInterviews > 0 ? round((($currentMonthInterviews - $lastMonthInterviews) / $lastMonthInterviews) * 100) : 0;

        // درصد پیشرفت (بر اساس تعداد کل)
        $maxPosts = 50;
        $maxNotes = 30;
        $maxInterviews = 20;

        $postsPercentage = $totalPosts > 0 ? min(100, round(($totalPosts / $maxPosts) * 100)) : 0;
        $notesPercentage = $totalNotes > 0 ? min(100, round(($totalNotes / $maxNotes) * 100)) : 0;
        $interviewsPercentage = $totalInterviews > 0 ? min(100, round(($totalInterviews / $maxInterviews) * 100)) : 0;

        return view('author.pages.dashboard', [
            'totalPosts' => $totalPosts,
            'totalNotes' => $totalNotes,
            'totalInterviews' => $totalInterviews,
            'publishedPosts' => $publishedPosts,
            'pendingPosts' => $pendingPosts,
            'draftPosts' => $draftPosts,
            'rejectedPosts' => $rejectedPosts,
            'todayPosts' => $todayPosts,
            'todayNotes' => $todayNotes,
            'todayInterviews' => $todayInterviews,
            'latestPosts' => $latestPosts,
            'postsGrowth' => $postsGrowth,
            'notesGrowth' => $notesGrowth,
            'interviewsGrowth' => $interviewsGrowth,
            'postsPercentage' => $postsPercentage,
            'notesPercentage' => $notesPercentage,
            'interviewsPercentage' => $interviewsPercentage,
            'author' => $author,
        ]);
    }
}
