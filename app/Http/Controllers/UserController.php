<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

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
}
