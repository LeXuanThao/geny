<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\LoginRequest;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Handle a login attempt.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        $user = User::where('email', $request->email)->first();

        if ($user && $user->isLockedOut()) {
            $lockoutTime = $user->lockout_time->diffForHumans();
            throw ValidationException::withMessages([
                'email' => "Your account is locked. Please try again in $lockoutTime.",
            ]);
        }

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user->resetFailedAttempts();

            return redirect()->intended('dashboard')->with('status', 'Welcome back!');
        }

        if ($user) {
            $user->incrementFailedAttempts();

            if ($user->failed_attempts >= config('auth.lockout.max_attempts')) {
                $user->lockout(config('auth.lockout.lockout_duration'));
                throw ValidationException::withMessages([
                    'email' => 'Your account is locked due to too many failed login attempts. Please try again later.',
                ]);
            }
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }
}
