<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login'); 
        }
    }

    // Show login form
    public function showLoginForm()
    {
        return view('auth.login'); 
    }

    // Handle login request
    public function login(Request $request)
    {
        // Validate email and password
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // Security: prevent session fixation
            return redirect()->intended('/admin/dashboard'); // Redirect after login
        }

        // Login failed
        return back()->withErrors([
            'email' => 'Invalid credentials',
        ])->onlyInput('email');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
