<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\BaleLinkCode;
use App\Models\Post;
use App\Services\BaleBotService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        /** @var \App\Models\Admin $author */
        $author = Auth::guard('admin')->user();
        $authorId = $author->id;

        $base = Post::where('author_id', $authorId);

        $totalPosts = (clone $base)->count();
        $publishedPosts = (clone $base)->where('status', 'published')->count();
        $pendingPosts = (clone $base)->where('status', 'pending')->count();
        $rejectedPosts = (clone $base)->where('status', 'rejected')->count();

        // آخرین اخبار
        $latestPosts = Post::with('category')
            ->where('author_id', $authorId)
            ->latest('created_at')
            ->limit(5)
            ->get();

        // وضعیت اتصال بله
        $bot = new BaleBotService();
        $baleLinked = filled($author->bale_chat_id);
        $hasActiveBaleCode = $baleLinked ? false : BaleLinkCode::where('admin_id', $authorId)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->exists();

        return view('author.pages.dashboard', [
            'author' => $author,
            'totalPosts' => $totalPosts,
            'publishedPosts' => $publishedPosts,
            'pendingPosts' => $pendingPosts,
            'rejectedPosts' => $rejectedPosts,
            'latestPosts' => $latestPosts,
            'baleLinked' => $baleLinked,
            'hasActiveBaleCode' => $hasActiveBaleCode,
            'botConfigured' => $bot->isConfigured(),
        ]);
    }
}
