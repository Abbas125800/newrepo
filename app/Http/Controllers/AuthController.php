<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'ایمیل یا رمز عبور نادرست است.'])
                ->onlyInput('email');
        }

        $user = $request->user();

        if ($user && $user->role === 'student' && ! $user->isApprovedStudent()) {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'حساب شاگرد شما هنوز توسط استاد تایید نشده است.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['teacher', 'student'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $userClass = config('auth.providers.users.model');

        $user = $userClass::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'approved_at' => $validated['role'] === 'teacher' ? now() : null,
            'password' => Hash::make($validated['password']),
        ]);

        if ($user->role === 'teacher') {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        return redirect()
            ->route('login')
            ->with('status', 'حساب شاگرد شما ساخته شد. بعد از تایید استاد می‌توانید وارد سیستم شوید.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
