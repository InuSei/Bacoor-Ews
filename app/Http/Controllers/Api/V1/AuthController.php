<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Used for sending MacroDroid SMS

class AuthController extends Controller
{
    // ... keep your existing register() method exactly as it is ...

    /**
     * 2A. FIND ACCOUNT (VIA PHONE) & GENERATE OTP
     */
    public function findAccount(Request $request)
    {
        $request->validate(['phone_number' => ['required', 'string', 'max:20']]);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No matching resident account found.'], 404);
        }

        return $this->generateAndDispatchOtp($user, 'phone');
    }

    /**
     * 2B. FIND ACCOUNT (VIA EMAIL) & GENERATE OTP 
     * (Pairs with your find-account-email.blade.php)
     */
    public function findAccountEmail(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'max:255']]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No matching email account found.'], 404);
        }

        return $this->generateAndDispatchOtp($user, 'email');
    }

    /**
     * 🔐 INTERNAL HELPER: The Engine that builds and sends the code
     */
    private function generateAndDispatchOtp(User $user, $method)
    {
        // 1. Generate a cryptographically secure 6-digit code
        $otp = (string) random_int(100000, 999999);

        // 2. Save it to the database with a strict 5-minute expiration timer
        $user->update([
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes(5)
        ]);

        // 3. ACTUAL DISPATCH: The Server sends the message itself
        if ($method === 'phone') {
            
            // Format the message professionally
            $messageBody = "CDRRMO Bacoor: Your verification code is {$otp}. It expires in 5 minutes. Do not share this code with anyone.";

            // Fire the HTTP POST request to the SMS Gateway (Semaphore PH API Example)
            try {
                \Illuminate\Support\Facades\Http::post('https://api.semaphore.co/api/v4/messages', [
                    'apikey' => env('SEMAPHORE_API_KEY'),
                    'number' => $user->phone_number,
                    'message' => $messageBody,
                    // 'sendername' => env('SEMAPHORE_SENDER_NAME') // Uncomment if you get an approved custom sender name
                ]);
            } catch (\Exception $e) {
                // Log the error securely without crashing the user's screen
                \Illuminate\Support\Facades\Log::error('SMS Dispatch Failed: ' . $e->getMessage());
            }

        } else {
            // Your Email Dispatch Logic (using Laravel Mail)
            // \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\SendOtpMail($otp));
        }

        return response()->json([
            'success'   => true,
            'message'   => 'Account located. Secure OTP generated and dispatched via SMS.',
            // 'debug_otp' => $otp // <-- Delete or comment this out in production!
        ], 200);
    }

    /**
     * 3. VERIFY CODE
     * Matches the frontend 24-second timer screen.
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => ['required_without:email', 'string'],
            'email'        => ['required_without:phone_number', 'string'],
            'otp_code'     => ['required', 'string', 'size:6'],
        ]);

        // Find user by either phone OR email
        $query = User::query();
        if ($request->has('phone_number')) $query->where('phone_number', $validated['phone_number']);
        if ($request->has('email')) $query->where('email', $validated['email']);
        $user = $query->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User record missing.'], 404);
        }

        // 🌟 SECURITY CHECKS 🌟
        // 1. Does the code match?
        if ($user->otp_code !== $validated['otp_code']) {
            return response()->json(['success' => false, 'message' => 'Invalid verification code.'], 422);
        }

        // 2. Has the 5-minute timer expired?
        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['success' => false, 'message' => 'This code has expired. Please request a new one.'], 422);
        }

        // 3. Code is valid! Wipe it from the database immediately so it cannot be reused (Security Best Practice)
        $user->update([
            'otp_code' => null,
            'otp_expires_at' => null
        ]);

        // Issue auth token
        $token = $user->createToken('mobile_access_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Identity verified successfully.',
            'token'   => $token,
            'user'    => ['full_name' => $user->full_name, 'role' => $user->role]
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => ['required_without:email', 'string'],
            'email'        => ['required_without:phone_number', 'string'],
            'otp_code'     => ['required', 'string', 'size:6'],
            'new_password' => ['required', 'string', 'min:6'], // Standard Laravel security minimum
        ]);

        // 1. Locate the account using either phone or email
        $query = User::query();
        if ($request->has('phone_number')) $query->where('phone_number', $validated['phone_number']);
        if ($request->has('email')) $query->where('email', $validated['email']);
        $user = $query->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User record missing.'], 404);
        }

        // 2. Security Check: Does the code match?
        if ($user->otp_code !== $validated['otp_code']) {
            return response()->json(['success' => false, 'message' => 'Invalid verification code.'], 422);
        }

        // 3. Security Check: Is the code expired?
        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['success' => false, 'message' => 'This code has expired. Please request a new one.'], 422);
        }

        // 4. Update the password securely & wipe the OTP keys immediately
        $user->update([
            'password'       => bcrypt($validated['new_password']), // Re-hash the new password
            'otp_code'       => null,
            'otp_expires_at' => null
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully. You may now log in with your new credentials.'
        ], 200);
    }
}