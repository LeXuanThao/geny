<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users with filters and pagination.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $users = Cache::remember('users', 60, function () use ($request) {
            return User::query()
                ->when($request->input('email'), function ($query, $email) {
                    return $query->where('email', 'like', "%{$email}%");
                })
                ->when($request->input('name'), function ($query, $name) {
                    return $query->where('name', 'like', "%{$name}%");
                })
                ->when($request->input('lockout_status'), function ($query, $lockoutStatus) {
                    return $query->where('lockout_status', $lockoutStatus);
                })
                ->orderBy('name')
                ->orderBy('email')
                ->orderBy('lockout_status')
                ->paginate(10);
        });

        return view('user.index', compact('users'));
    }

    /**
     * Show the user creation form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('user.create');
    }

    /**
     * Handle a user creation request.
     *
     * @param  \App\Http\Requests\CreateUserRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(CreateUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'profile_picture' => $request->profile_picture,
            'date_of_birth' => $request->date_of_birth,
        ]);

        // Send email verification
        $user->sendEmailVerificationNotification();

        return redirect()->route('login')->with('status', 'User created successfully. Please check your email to verify your account.');
    }
}
