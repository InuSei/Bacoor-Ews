<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ResidentAuthController extends Controller
{
    /**
     * MASTER LOGIN DOOR: Process the standard web login form submission.
     */
    public function processLogin(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
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

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
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
        $validated = $request->validate([
            'full_name'    => ['required', 'string', 'max:255'],
            'username'     => ['required', 'string', 'max:50', 'unique:users,username'],
            'phone_number' => ['required', 'string', 'max:20', 'unique:users,phone_number'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'barangay'     => ['required', 'string', 'max:50'], 
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'full_name'    => $validated['full_name'],
            'username'     => $validated['username'], 
            'phone_number' => $validated['phone_number'],
            'email'        => $validated['email'],
            'barangay'     => $validated['barangay'], 
            'password'     => Hash::make($validated['password']),
            'role'         => 'resident',
        ]);

        Auth::login($user);

        return redirect()->route('resident.map-dashboard')->with('success', 'Account created successfully!');
    }

    public function showFindAccount(Request $request) 
    { 
        // 🌟 CENTRAL STATE REGISTRATION: Keep track of why the user is checking details
        if ($request->routeIs('resident.forgot-password')) {
            session(['auth_flow' => 'reset']);
        } else {
            session(['auth_flow' => 'login']);
        }
        return view('resident.find-account'); 
    }

    public function processFindAccount(Request $request)
    {
        $request->validate(['phone_number' => ['required', 'string', 'max:20']]);
        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return back()->withErrors(['phone_number' => 'No matching CDRRMO resident account found.'])->withInput();
        }

        // Native OTP Dispatch Trigger
        $this->generateAndDispatchOtp($user, 'phone');

        // Store active key identity metadata into session variables cleanly
        session(['auth_phone' => $request->phone_number]);

        // 🌟 DECOUPLED DISPATCH ROUTING
        if (session('auth_flow') === 'reset') {
            return redirect()->route('resident.reset-password');
        }

        return redirect()->route('resident.verify-otp');
    }

    public function showFindAccountEmail(Request $request) 
    { 
        // Leaves the auth_flow state session unchanged if they switch from phone mid-navigation
        return view('resident.find-account-email'); 
    }

    public function processFindAccountEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'max:255']]);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No matching CDRRMO account found.'])->withInput();
        }

        $this->generateAndDispatchOtp($user, 'email');

        // Store active email data attributes inside temporary session parameters
        session(['auth_email' => $user->email]);

        // 🌟 SYMMETRICAL FLOW REDIRECTION FOR EMAIL
        if (session('auth_flow') === 'reset') {
            return redirect()->route('resident.reset-password');
        }

        return redirect()->route('resident.verify-otp');
    }

    /**
     * 🔐 CORE OTP DISPATCH ENGINE
     */
    private function generateAndDispatchOtp(User $user, $method)
    {
        $otp = (string) random_int(100000, 999999);

        $user->update([
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes(5)
        ]);

        if ($method === 'phone') {
            $cleanNumber = preg_replace('/[^0-9]/', '', $user->phone_number);
            $messageBody = "CDRRMO Bacoor: Your verification code is {$otp}. It expires in 5 minutes. Do not share this code.";
            
            // 🌟 PRESENTATION SAFETY NET: Local simulation logging to prevent Semaphore paywall crashes
            Log::info("=== SMS SYSTEM FALLBACK (PRESENTATION MODE) ===");
            Log::info("To: {$cleanNumber} | Payload Content: {$messageBody}");
            return;
        } 
        
        try {
            // 🌟 DELEGATED RESPONSIBILITY: Controller simply tells Laravel to send the Mailable
            Mail::to($user->email)->send(new SendOtpMail($otp));
            Log::info("Native Laravel Mail safely dispatched OTP to: " . $user->email);
        } catch (\Exception $e) {
            Log::error('Mail Dispatch Failure: ' . $e->getMessage());
        }
    }

    public function showVerifyOtp(Request $request)
    {
        // Accept navigation if either email or phone is securely cached
        if (!session()->has('auth_phone') && !session()->has('auth_email')) {
            return redirect()->route('resident.find-account');
        }
        return view('resident.verify-otp');
    }

    public function processVerifyOtp(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => ['required_without:email', 'nullable', 'string'],
            'email'        => ['required_without:phone_number', 'nullable', 'string'],
            'otp_code'     => ['required', 'string', 'size:6'],
        ]);

        $query = User::query();
        if ($request->filled('phone_number')) $query->where('phone_number', $validated['phone_number']);
        if ($request->filled('email')) $query->where('email', $validated['email']);
        
        $user = $query->first();

        if (!$user) {
            return redirect()->route('resident.find-account')->withErrors(['phone_number' => 'User record missing.']);
        }

        if ((string)$user->otp_code !== (string)$validated['otp_code']) {
            return back()->withErrors(['otp_code' => 'The verification passcode is invalid.']);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'This code has expired. Please request a new one.']);
        }

        $user->update(['otp_code' => null, 'otp_expires_at' => null]);
        session()->forget(['auth_phone', 'auth_email', 'auth_flow']);

        Auth::login($user);
        return redirect()->route('resident.map-dashboard');
    }

    public function showResetPassword()
    {
        if (!session('auth_phone') && !session('auth_email')) {
            return redirect()->route('resident.find-account')->withErrors(['phone_number' => 'Session expired. Please request a new code.']);
        }
        return view('resident.reset-password');
    }

    public function processResetPassword(Request $request)
    {
        $request->validate([
            'phone_number' => ['required_without:email', 'nullable', 'string'],
            'email'        => ['required_without:phone_number', 'nullable', 'string'],
            'otp_code'     => ['required', 'string', 'size:6'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $query = User::query();
        if ($request->filled('phone_number')) {
            $query->where('phone_number', $request->phone_number);
        } else {
            $query->where('email', $request->email);
        }
        $user = $query->first();

        if (!$user) {
            return back()->withErrors(['otp_code' => 'User record not found.']);
        }

        if ((string)$user->otp_code !== (string)$request->otp_code) {
            return back()->withErrors(['otp_code' => 'Invalid verification code.']);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return back()->withErrors(['otp_code' => 'This code has expired. Please request a new one.']);
        }

        $user->update([
            'password'       => Hash::make($request->new_password),
            'otp_code'       => null,
            'otp_expires_at' => null
        ]);

        session()->forget(['auth_phone', 'auth_email', 'auth_flow']);

        return redirect()->route('login')->withErrors(['username' => 'Password reset successfully! Please log in with your new credentials.']); 
    }

    /**
     * RESTORED MAP METHOD
     */
    public function showUserMap()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        return view('resident.map-dashboard');
    }
}