<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard'); // define this route
            } elseif ($user->role === 'staff') {
                return redirect()->route('staff.dashboard'); // define this route
            } else {
                Auth::logout();
                return back()->with('error', 'Unauthorized role.');
            }
        }

        return back()->with('error', 'Invalid login credentials.');
    }

}
