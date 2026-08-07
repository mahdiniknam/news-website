<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\HomeController;
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

    Route::middleware('auth:admin')->group(function () {

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
            Route::get('/', [PostController::class, 'index'])->name('index');
            Route::get('/create', [PostController::class, 'create'])->name('create');
            Route::post('/store', [PostController::class, 'store'])->name('store');
            Route::get('/edit/{post}', [PostController::class, 'edit'])->name('edit');
            Route::put('/update/{post}', [PostController::class, 'update'])->name('update');
            Route::delete('/destroy/{post}', [PostController::class, 'destroy'])->name('destroy');
        });
    });
});



Route::prefix('author')->name('author.')->group(function () {

    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'loginWithPassword'])->name('login.post');

        Route::post('/login/verify', [LoginController::class, 'verifyLoginChallenge'])->name('login.verify');
    });

    Route::middleware('auth:author')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('show.dashboard');
    });

    Route::middleware('auth:author')->group(function () {

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        Route::prefix('posts')->name('posts.')->group(function () {
            Route::get('/', [PostController::class, 'index'])->name('index');
            Route::get('/create', [PostController::class, 'create'])->name('create');
            Route::post('/store', [PostController::class, 'store'])->name('store');
            Route::get('/edit/{post}', [PostController::class, 'edit'])->name('edit');
            Route::put('/update/{post}', [PostController::class, 'update'])->name('update');
            Route::delete('/destroy/{post}', [PostController::class, 'destroy'])->name('destroy');
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

// لیست مصاحبه‌ها
Route::get('/interviews', [HomeController::class, 'allInterviews'])->name('interviews');
