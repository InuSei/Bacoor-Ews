<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ResidentAuthController extends Controller
{
    /**
     * MASTER LOGIN DOOR: Process the standard web login form submission.
     */
    public function processLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'], // The HTML input name is still 'username'
            'password' => ['required', 'string'],
            'role'     => ['required', 'string'],
        ]);

        $loginInput = $credentials['username'];

        // 🌟 SMART QUERY: Checks if input matches Email, Username, OR Phone Number
        $user = User::where('role', $credentials['role'])
                    ->where(function ($query) use ($loginInput) {
                        $query->where('email', $loginInput)
                              ->orWhere('username', $loginInput)
                              ->orWhere('phone_number', $loginInput);
                    })->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'auth_error' => 'The provided credentials do not match our disaster monitoring records.',
            ])->withInput();
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        }
        return redirect()->route('resident.map-dashboard');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'resident') {
                return redirect()->route('resident.map-dashboard');
            }
            return redirect()->route('dashboard');
        }
        return view('resident.register');
    }

    public function processRegister(Request $request)
    {
        // 1. Strict Validation (Now specifically requiring a unique username)
        $validated = $request->validate([
            'full_name'    => ['required', 'string', 'max:255'],
            'username'     => ['required', 'string', 'max:50', 'unique:users,username'], // 🌟 Custom Username Added!
            'phone_number' => ['required', 'string', 'max:20', 'unique:users,phone_number'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'barangay'     => ['required', 'string', 'max:50'], 
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // 2. Database Insertion (No more auto-generation!)
        $user = User::create([
            'full_name'    => $validated['full_name'],
            'username'     => $validated['username'], // 🌟 Saving their chosen username
            'phone_number' => $validated['phone_number'],
            'email'        => $validated['email'],
            'barangay'     => $validated['barangay'], 
            'password'     => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role'         => 'resident',
        ]);

        Auth::login($user);

        return redirect()->route('resident.map-dashboard')->with('success', 'Account created successfully!');
    }

    public function showFindAccount() { return view('resident.find-account'); }

    public function processFindAccount(Request $request)
    {
        $request->validate(['phone_number' => ['required', 'string', 'max:20']]);
        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return back()->withErrors(['phone_number' => 'No matching CDRRMO resident account found.'])->withInput();
        }
        return redirect()->route('resident.verify-otp')->with('auth_phone', $request->phone_number);
    }

    public function showFindAccountEmail() { return view('resident.find-account-email'); }

    public function processFindAccountEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'max:255']]);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No matching CDRRMO account found.'])->withInput();
        }
        return redirect()->route('resident.verify-otp')->with('auth_phone', $user->phone_number)->with('success', 'Account verified! Simulated OTP dispatch.');
    }

    public function showVerifyOtp(Request $request)
    {
        if (!session()->has('auth_phone')) {
            return redirect()->route('resident.find-account');
        }
        return view('resident.verify-otp');
    }

    public function processVerifyOtp(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string'],
            'otp_code'     => ['required', 'string', 'size:6'],
        ]);

        if ($validated['otp_code'] !== '123456') {
            return back()->withErrors(['otp_code' => 'The verification passcode is invalid or expired.']);
        }

        $user = User::where('phone_number', $validated['phone_number'])->first();

        if (!$user) {
            return redirect()->route('resident.find-account')->withErrors(['phone_number' => 'User record missing.']);
        }

        Auth::login($user);
        return redirect()->route('resident.map-dashboard');
    }

    public function showUserMap()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return view('resident.map-dashboard');
    }
}