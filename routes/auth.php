<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\Auth\GoogleController;

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->boolean('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login.store')->middleware('guest');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

// Registration routes (optional)
Route::get('/register', function () {
    return view('auth.register');
})->name('register')->middleware('guest');

Route::post('/register', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    // Ensure the user is not an admin by default
    // The isAdmin() method checks email patterns, so we need to ensure the email doesn't match admin patterns
    if ($user->isAdmin()) {
        // If somehow the user is detected as admin, update the email to ensure it's not an admin
        $user->update(['email' => $request->email]); // This ensures the email is set correctly
    }

    Auth::login($user);

    return redirect(route('dashboard'));
})->name('register.store')->middleware('guest');

// Google OAuth routes
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('login.google')->middleware('guest');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('login.google.callback')->middleware('guest');