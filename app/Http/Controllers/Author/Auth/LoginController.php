<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthorAuthRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('author.pages.auth.login');
    }

    public function loginWithPassword(AuthorAuthRequest $request)
    {
        $data = $request->only('email', 'password');

        $isLoggedIn = Auth::guard('admin')->attempt($data);

        if (!$isLoggedIn) {
            return back()->withInput($request->only('emial'))->withErrors([
                'email' => 'ایمیل یا رمز عبور صحیح نمیباشد',
            ]);
        }
        $request->session()->regenerate();

        return redirect()->intended(route('author.show.dashboard'))->with('success', 'ورود موفقیت آمیز');
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
        return redirect()->route('admin.login')->with('success', 'با موفقیت خارج شدید.');
    }
}
