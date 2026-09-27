<?php

use App\Http\Controllers\Account\BaleLinkController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BaleSettingsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Author\Auth\LoginController as AuthLoginController;
use App\Http\Controllers\Author\DashboardController as AuthorDashboardController;
use App\Http\Controllers\Author\PostController as AuthorPostController;
use App\Http\Controllers\BaleWebhookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostReviewController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('admin.pages.dashboard');
// });

Route::prefix('didebaneshahr/admin')->name('admin.')->group(function () {

    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'loginWithPassword'])->name('login.post');

        Route::post('/login/verify', [LoginController::class, 'verifyLoginChallenge'])->name('login.verify');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('show.dashboard');
    });

    /*
     | تنظیمات ربات بله — فقط super-admin
     */
    Route::middleware(['auth:admin', 'role:super-admin'])->group(function () {
        Route::get('/bale-settings', [BaleSettingsController::class, 'index'])->name('bale.index');
        Route::post('/bale-settings', [BaleSettingsController::class, 'store'])->name('bale.store');
    });

    /*
     | بخش‌های مدیریتی — فقط admin و super-admin
     | نویسنده (author) به هیچ‌کدام دسترسی ندارد.
     */
    Route::middleware(['auth:admin', 'role:admin'])->group(function () {

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/store', [RoleController::class, 'store'])->name('store');

            Route::get('/edit/{role}', [RoleController::class, 'edit'])->name('edit');
            Route::put('/update/{role}', [RoleController::class, 'update'])->name('update');
        });


        Route::prefix('admins')->name('admins.')->group(function () {
            Route::get('/', [AdminController::class, 'index'])->name('index');
            Route::get('/create', [AdminController::class, 'create'])->name('create');
            Route::post('/store', [AdminController::class, 'store'])->name('store');
            Route::get('/edit/{admin}', [AdminController::class, 'edit'])->name('edit');
            Route::post('/update/{admin}', [AdminController::class, 'update'])->name('update');
        });

        Route::prefix('tags')->name('tags.')->group(function () {
            Route::get('/', [TagController::class, 'index'])->name('index');
            Route::post('/store', [TagController::class, 'store'])->name('store');
            Route::get('/edit/{tag}', [TagController::class, 'edit'])->name('edit');
            Route::put('/update/{tag}', [TagController::class, 'update'])->name('update');
            Route::delete('/destroy/{tag}', [TagController::class, 'destroy'])->name('destroy');
        });


        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::post('/store', [CategoryController::class, 'store'])->name('store');
            Route::get('/edit/{category}', [CategoryController::class, 'edit'])->name('edit');
            Route::put('/update/{category}', [CategoryController::class, 'update'])->name('update');
            Route::delete('/destroy/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('posts')->name('posts.')->group(function () {
            // مسیرهای بدون پارامتر (ثابت) باید اول تعریف شوند
            Route::get('/create', [PostController::class, 'create'])->name('create');
            Route::post('/store', [PostController::class, 'store'])->name('store');
            Route::get('/', [PostController::class, 'index'])->name('index');

            // مسیرهای با پارامتر {post} (متغیر) باید بعد از مسیرهای ثابت تعریف شوند
            Route::post('{post}/approve', [PostController::class, 'approve'])->name('approve');
            Route::post('{post}/reject', [PostController::class, 'reject'])->name('reject');
            Route::post('{post}/publish', [PostController::class, 'publish'])->name('publish');
            Route::get('{post}/rejection-reason', [PostController::class, 'showRejectionReason'])->name('rejection-reason');

            // مسیرهای CRUD اصلی با {post} باید در انتها تعریف شوند
            Route::get('edit/{post}', [PostController::class, 'edit'])->name('edit');
            Route::put('update/{post}', [PostController::class, 'update'])->name('update');
            Route::delete('destroy/{post}', [PostController::class, 'destroy'])->name('destroy');
        });
    });
});









Route::prefix('author')->name('author.')->group(function () {

    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthLoginController::class, 'loginWithPassword'])->name('login.post');

        Route::post('/login/verify', [AuthLoginController::class, 'verifyLoginChallenge'])->name('login.verify');
    });

    /*
     | پنل نویسنده — فقط نقش author (ادمین‌ها از پنل خودشان استفاده کنند)
     | دسترسی فقط به: داشبورد، ثبت/ویرایش/حذف خبر خودش
     */
    Route::middleware(['auth:admin', 'role:author'])->group(function () {
        Route::get('/dashboard', [AuthorDashboardController::class, 'dashboard'])->name('show.dashboard');

        Route::post('/logout', [AuthLoginController::class, 'logout'])->name('logout');

        Route::prefix('posts')->name('posts.')->group(function () {
            Route::get('/', [AuthorPostController::class, 'index'])->name('index');
            Route::get('/create', [AuthorPostController::class, 'create'])->name('create');
            Route::post('/store', [AuthorPostController::class, 'store'])->name('store');
            Route::get('/edit/{post}', [AuthorPostController::class, 'edit'])->name('edit');
            Route::put('/update/{post}', [AuthorPostController::class, 'update'])->name('update');
            Route::delete('/destroy/{post}', [AuthorPostController::class, 'destroy'])->name('destroy');
            Route::get('{post}/rejection-reason', [AuthorPostController::class, 'showRejectionReason'])->name('rejection-reason');
        });
    });
});















// صفحه اصلی
Route::get('/', [HomeController::class, 'index'])->name('home');

// نمایش خبر
Route::get('/news/{slug}', [HomeController::class, 'show'])->name('post.show');

// لیست اخبار بر اساس دسته‌بندی
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

// لیست همه اخبار
Route::get('/news', [HomeController::class, 'allNews'])->name('news');

// جستجو
Route::get('/search', [HomeController::class, 'search'])->name('news.search');

// آرشیو بر اساس تاریخ
Route::get('/archive/{year}/{month}', [HomeController::class, 'archive'])->name('news.archive');

// RSS Feed (اختیاری)
Route::get('/feed', [HomeController::class, 'feed'])->name('news.feed');

// لیست یادداشت‌ها
Route::get('/notes', [HomeController::class, 'allNotes'])->name('notes');

/*
|--------------------------------------------------------------------------
| آپلود تصویر داخل ویرایشگر (CKEditor) — برای نویسنده و ادمین
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')->group(function () {
    Route::post('/editor/upload-image', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'upload' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ], [
            'upload.required' => 'فایلی انتخاب نشده است',
            'upload.image' => 'فایل باید تصویر باشد',
            'upload.max' => 'حجم تصویر نباید بیشتر از ۵ مگابایت باشد',
        ]);

        $path = $request->file('upload')->store('posts/content', 'public');

        return response()->json([
            'uploaded' => 1,
            'fileName' => basename($path),
            'url' => asset('storage/' . $path),
        ]);
    })->name('editor.upload');
});

/*
|--------------------------------------------------------------------------
| حساب کاربری مشترک (نویسنده و ادمین): پروفایل و اتصال ربات بله
|--------------------------------------------------------------------------
*/
Route::middleware('auth:admin')->group(function () {
    Route::get('/account/profile', [ProfileController::class, 'edit'])->name('account.profile');
    Route::put('/account/profile', [ProfileController::class, 'update'])->name('account.profile.update');

    Route::get('/account/bale', [BaleLinkController::class, 'index'])->name('account.bale');
    Route::post('/account/bale/code', [BaleLinkController::class, 'createCode'])->name('account.bale.code');
    Route::post('/account/bale/unlink', [BaleLinkController::class, 'unlink'])->name('account.bale.unlink');
});

/*
| Webhook ربات بله — مسیر با توکن تصادفی ایمن می‌شود و فقط از سمت سرورهای بله صدا زده می‌شود.
| آدرس webhook: {APP_URL}/bale-webhook/{BALE_WEBHOOK_SECRET}
*/
Route::post('/bale-webhook/{secret}', BaleWebhookController::class)
    ->name('bale.webhook')
    ->middleware('throttle:120,1');

// لیست مصاحبه‌ها
Route::get('/interviews', [HomeController::class, 'allInterviews'])->name('interviews');

/*
|--------------------------------------------------------------------------
| بررسی خبر از طریق لینک امن ربات بله
|--------------------------------------------------------------------------
| دسترسی با توکن ۴۸ کاراکتری یک‌بارمصرف (هش‌شده در دیتابیس) انجام می‌شود.
| نمایش با GET و تصمیم‌گیری (تایید/رد) فقط با POST و CSRF ممکن است.
| مهم: روت «result» باید قبل از روت پارامتری {token} ثبت شود تا قاطی نشود.
*/
Route::get('/post-review/result', [PostReviewController::class, 'result'])->name('posts.review.result');

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/post-review/{token}', [PostReviewController::class, 'show'])->name('posts.review.show');
    Route::post('/post-review/{token}/decide', [PostReviewController::class, 'decide'])->name('posts.review.decide');
});
