<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostReviewToken;
use App\Services\PostNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

/**
 * بررسی خبر توسط مدیر از طریق لینک امنی که ربات بله ارسال کرده است.
 *
 * امنیت:
 * - توکن ۴۸ کاراکتری تصادفی؛ در دیتابیس فقط هش SHA-256 ذخیره می‌شود.
 * - توکن ۳ روز اعتبار دارد و فقط یک بار قابل استفاده است.
 * - تصمیم (تایید/رد) فقط با POST و توکن CSRF انجام می‌شود؛ لینک GET فقط نمایش است.
 * - برای جلوگیری از حدس توکن، rate limit روی هر دو مسیر اعمال می‌شود.
 */
class PostReviewController extends Controller
{
    /**
     * نمایش صفحه بررسی خبر با توکن (بدون نیاز به لاگین، اما اگر مدیر لاگین باشد خوش‌آمد گویی می‌شود).
     */
    public function show(Request $request, string $token)
    {
        $reviewToken = $this->findValidToken($token);

        if (! $reviewToken) {
            return response()->view('review.link-expired', [], 410);
        }

        $post = $reviewToken->post()->with(['author', 'category', 'approver'])->firstOrFail();

        return view('review.show', [
            'post' => $post,
            'token' => $token,
            'isLoggedInAdmin' => Auth::guard('admin')->check(),
        ]);
    }

    /**
     * تصمیم نهایی مدیر: تایید یا رد (با دلیل).
     */
    public function decide(Request $request, string $token, PostNotifier $notifier)
    {
        // ضد حدس توکن: حداکثر ۲۰ تلاش در دقیقه برای هر IP
        $executed = RateLimiter::attempt(
            key: 'post-review:' . $request->ip(),
            maxAttempts: 20,
            callback: fn () => $this->processDecision($request, $token, $notifier),
            decaySeconds: 60,
        );

        if (! $executed) {
            return redirect()
                ->route('posts.review.show', ['token' => $token])
                ->with('error', 'تلاش‌های بیش از حد؛ لطفاً یک دقیقه بعد دوباره تلاش کنید.');
        }

        return $executed;
    }

    protected function processDecision(Request $request, string $token, PostNotifier $notifier)
    {
        $reviewToken = $this->findValidToken($token);

        if (! $reviewToken) {
            return response()->view('review.link-expired', [], 410);
        }

        $action = $request->input('action');

        $request->validate(
            [
                'action' => 'required|in:approve,reject',
                'rejection_reason' => 'required_if:action,reject|string|min:5|max:500',
            ],
            [
                'action.required' => 'نوع تصمیم مشخص نیست',
                'action.in' => 'نوع تصمیم معتبر نیست',
                'rejection_reason.required_if' => 'نوشتن دلیل رد الزامی است',
                'rejection_reason.min' => 'دلیل رد باید حداقل ۵ کاراکتر باشد',
                'rejection_reason.max' => 'دلیل رد نمی‌تواند بیشتر از ۵۰۰ کاراکتر باشد',
            ]
        );

        $post = $reviewToken->post;

        // فقط خبرهای در انتظار تایید قابل تصمیم‌گیری هستند
        if ($post->status !== 'pending') {
            return redirect()
                ->route('posts.review.show', ['token' => $token])
                ->with('error', 'این خبر قبلاً بررسی شده است و قابل تغییر نیست.');
        }

        $admin = Auth::guard('admin')->user();
        $adminId = $admin?->id;

        DB::transaction(function () use ($request, $reviewToken, $post, $action, $adminId, $notifier) {
            // مصرف یک‌بارِ توکن
            if (! $reviewToken->consume($request->ip())) {
                abort(410, 'این لینک قبلاً استفاده شده است.');
            }

            if ($action === 'approve') {
                $post->status = 'approved';
                $post->approved_by = $adminId;
                $post->approved_at = now();
                $post->save();

                $notifier->notifyAuthorApproved($post);
            } else {
                $post->status = 'rejected';
                $post->rejection_reason = $request->input('rejection_reason');
                $post->save();

                $notifier->notifyAuthorRejected($post);
            }
        });

        return redirect()
            ->route('posts.review.result')
            ->with('review_result', [
                'approved' => $action === 'approve',
                'title' => $post->title,
            ]);
    }

    /**
     * صفحه نتیجه تصمیم (بعد از POST و ریدایرکت).
     */
    public function result()
    {
        $result = session('review_result');

        if (! $result) {
            return redirect()->route('home');
        }

        return view('review.result', ['result' => $result]);
    }

    /**
     * یافتن توکن معتبر با هش امن و مقایسه زمان‌ثابت.
     */
    protected function findValidToken(string $token): ?PostReviewToken
    {
        if (! is_string($token) || strlen($token) < 32 || strlen($token) > 64) {
            return null;
        }

        $hash = hash('sha256', $token);

        // برای جلوگیری از حملات زمان‌بندی، با hash_equals مقایسه می‌کنیم
        $candidate = PostReviewToken::where('token_hash', $hash)->first();

        if (! $candidate || ! hash_equals($candidate->token_hash, $hash) || ! $candidate->isValid()) {
            return null;
        }

        return $candidate;
    }
}
