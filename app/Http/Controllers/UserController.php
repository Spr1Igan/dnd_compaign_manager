<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('success', __('ui.messages.register_success'));
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('login', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()
                ->intended(route('home'))
                ->with('success', __('ui.messages.login_success'));
        }

        return back()
            ->withErrors([
                'login' => __('ui.messages.invalid_login'),
            ])
            ->onlyInput('login');
    }

    public function logout(): RedirectResponse
    {
        Auth::logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', __('ui.messages.logout_success'));
    }

    public function profile(): View
    {
        return view('auth.profile');
    }
}
