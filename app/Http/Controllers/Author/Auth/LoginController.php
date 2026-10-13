<?php

namespace App\Http\Controllers\Author\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthorAuthRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('author.show.dashboard');
        }

        if ($request->has('expired')) {
            session()->flash('error', 'نشست شما منقضی شده است، لطفا دوباره وارد شوید');
        }

        return view('author.pages.auth.login');
    }

    public function loginWithPassword(AuthorAuthRequest $request)
    {
        $data = $request->only('email', 'password');

        $isLoggedIn = Auth::guard('admin')->attempt($data);

        if (!$isLoggedIn) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'ایمیل یا رمز عبور صحیح نمی‌باشد',
            ]);
        }

        $user = Auth::guard('admin')->user();

        if ($user && !$user->is_active) {
            Auth::guard('admin')->logout();

            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'حساب شما غیرفعال است',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('author.show.dashboard'))->with('success', 'ورود موفقیت‌آمیز');
    }

    public function logout(Request $request)
    {
        // خروج از گارد ادمین
        Auth::guard('admin')->logout();

        // غیرفعال کردن سشن
        $request->session()->invalidate();

        // بازسازی توکن CSRF
        $request->session()->regenerateToken();

        // ریدایرکت به صفحه لاگین با پیام
        return redirect()->route('author.login')->with('success', 'با موفقیت خارج شدید.');
    }
}
