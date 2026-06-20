<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validate the request (assuming your HTML form input is still named 'username')
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginValue = $request->input('username');
        $password = $request->input('password');

        // 2. Check if the input is a valid email format
        $fieldType = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // 3. Build the credentials array dynamically based on what they typed
        $credentials = [
            $fieldType => $loginValue,
            'password' => $password,
        ];

        // 4. Attempt login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('pumps.maps'));
        }

        // 5. If it fails, send them back with an error and keep what they typed
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
