<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {

        // تعداد کل
        $totalPosts = Post::count();
        $totalNotes = Post::whereHas('category', function ($q) {
            $q->where('slug', 'yaddasht')->orWhere('name', 'یادداشت');
        })->count();
        $totalInterviews = Post::whereHas('category', function ($q) {
            $q->where('slug', 'mosabehe')->orWhere('name', 'مصاحبه');
        })->count();
        $totalUsers = Admin::count();

        // وضعیت اخبار
        $publishedPosts = Post::where('status', 'published')->count();
        $pendingPosts = Post::where('status', 'pending')->count();
        $draftPosts = Post::where('status', 'draft')->count();
        $rejectedPosts = Post::where('status', 'rejected')->count();

        // آمار امروز
        $today = Carbon::today();
        $todayPosts = Post::whereDate('created_at', $today)->count();
        $todayNotes = Post::whereHas('category', function ($q) {
            $q->where('slug', 'yaddasht')->orWhere('name', 'یادداشت');
        })->whereDate('created_at', $today)->count();
        $todayInterviews = Post::whereHas('category', function ($q) {
            $q->where('slug', 'mosabehe')->orWhere('name', 'مصاحبه');
        })->whereDate('created_at', $today)->count();
        $todayUsers = Admin::whereDate('created_at', $today)->count();

        // آخرین اخبار
        $latestPosts = Post::with(['author', 'category'])
            ->latest('created_at')
            ->limit(5)
            ->get();

        // درصد رشد (مثال)
        $lastMonth = Carbon::now()->subMonth();
        $currentMonthPosts = Post::whereMonth('created_at', Carbon::now()->month)->count();
        $lastMonthPosts = Post::whereMonth('created_at', $lastMonth->month)->count();
        $postsGrowth = $lastMonthPosts > 0 ? round((($currentMonthPosts - $lastMonthPosts) / $lastMonthPosts) * 100) : 0;

        // درصد پیشرفت (مثال)
        $postsPercentage = $totalPosts > 0 ? min(100, round(($totalPosts / 100) * 100)) : 0;
        $notesPercentage = $totalNotes > 0 ? min(100, round(($totalNotes / 100) * 100)) : 0;
        $interviewsPercentage = $totalInterviews > 0 ? min(100, round(($totalInterviews / 100) * 100)) : 0;
        $usersPercentage = $totalUsers > 0 ? min(100, round(($totalUsers / 100) * 100)) : 0;

        // محاسبه درصد رشد برای هر بخش
        $lastMonth = Carbon::now()->subMonth();

        // رشد اخبار
        $currentMonthPosts = Post::whereMonth('created_at', Carbon::now()->month)->count();
        $lastMonthPosts = Post::whereMonth('created_at', $lastMonth->month)->count();
        $postsGrowth = $lastMonthPosts > 0 ? round((($currentMonthPosts - $lastMonthPosts) / $lastMonthPosts) * 100) : 0;

        // رشد یادداشت‌ها
        $currentMonthNotes = Post::whereHas('category', function ($q) {
            $q->where('slug', 'yaddasht')->orWhere('name', 'یادداشت');
        })->whereMonth('created_at', Carbon::now()->month)->count();
        $lastMonthNotes = Post::whereHas('category', function ($q) {
            $q->where('slug', 'yaddasht')->orWhere('name', 'یادداشت');
        })->whereMonth('created_at', $lastMonth->month)->count();
        $notesGrowth = $lastMonthNotes > 0 ? round((($currentMonthNotes - $lastMonthNotes) / $lastMonthNotes) * 100) : 0;

        // رشد مصاحبه‌ها
        $currentMonthInterviews = Post::whereHas('category', function ($q) {
            $q->where('slug', 'mosabehe')->orWhere('name', 'مصاحبه');
        })->whereMonth('created_at', Carbon::now()->month)->count();
        $lastMonthInterviews = Post::whereHas('category', function ($q) {
            $q->where('slug', 'mosabehe')->orWhere('name', 'مصاحبه');
        })->whereMonth('created_at', $lastMonth->month)->count();
        $interviewsGrowth = $lastMonthInterviews > 0 ? round((($currentMonthInterviews - $lastMonthInterviews) / $lastMonthInterviews) * 100) : 0;

        // رشد کاربران
        $currentMonthUsers = Admin::whereMonth('created_at', Carbon::now()->month)->count();
        $lastMonthUsers = Admin::whereMonth('created_at', $lastMonth->month)->count();
        $usersGrowth = $lastMonthUsers > 0 ? round((($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100) : 0;
        
        return view('admin.pages.dashboard', [
            'totalPosts' => $totalPosts,
            'totalNotes' => $totalNotes,
            'totalInterviews' => $totalInterviews,
            'totalUsers' => $totalUsers,
            'publishedPosts' => $publishedPosts,
            'pendingPosts' => $pendingPosts,
            'draftPosts' => $draftPosts,
            'rejectedPosts' => $rejectedPosts,
            'todayPosts' => $todayPosts,
            'todayNotes' => $todayNotes,
            'todayInterviews' => $todayInterviews,
            'todayUsers' => $todayUsers,
            'latestPosts' => $latestPosts,
            'postsGrowth' => $postsGrowth,
            'notesGrowth' => $notesGrowth,
            'interviewsGrowth' => $interviewsGrowth,
            'usersGrowth' => $usersGrowth,
            'postsPercentage' => $postsPercentage,
            'notesPercentage' => $notesPercentage,
            'interviewsPercentage' => $interviewsPercentage,
            'usersPercentage' => $usersPercentage,
        ]);
    }
}
