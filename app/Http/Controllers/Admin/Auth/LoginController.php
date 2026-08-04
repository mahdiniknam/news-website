<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminAuthRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.pages.auth.login');
    }

    public function loginWithPassword(AdminAuthRequest $request)
    {
        $data = $request->only('email', 'password');

        $isLoggedIn = Auth::guard('admin')->attempt($data);

        if (!$isLoggedIn) {
            return back()->withInput($request->only('emial'))->withErrors([
                'email' => 'ایمیل یا رمز عبور صحیح نمیباشد',
            ]);
        }
        $request->session()->regenerate();

        return redirect()->intended(route('admin.show.dashboard'))->with('success', 'ورود موفقیت آمیز');
    }
}
