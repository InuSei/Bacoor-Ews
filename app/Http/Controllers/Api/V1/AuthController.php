<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * 1. USER SIGNUP / REGISTER
     * Maps perfectly to your database schema requirements.
     */
    public function register(Request $request)
    {
        // Validation rules matching your actual structural database data fields
        $validated = $request->validate([
            'full_name'    => ['required', 'string', 'max:255'],
            'username'     => ['required', 'string', 'unique:users,username', 'max:50'],
            'phone_number' => ['required', 'string', 'unique:users,phone_number', 'max:20'],
            'password'     => ['required', 'string', 'min:6'],
            'barangay_id'  => ['required', 'integer'],
        ]);

        // Uncle Bob Standard: Explicit database property allocation mapping
        $user = User::create([
            'full_name'    => $validated['full_name'],
            'username'     => $validated['username'],
            'phone_number' => $validated['phone_number'],
            'password'     => bcrypt($validated['password']),
            'role'         => 'resident', // Securely defaults to your database 'resident' role structure
            'barangay_id'  => $validated['barangay_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resident citizen account registered successfully under disaster tracking modules.',
            'data'    => $user
        ], 201);
    }

    /**
     * 2. FIND ACCOUNT
     * Matches the mobile interface: "Enter your mobile number" screen.
     */
    public function findAccount(Request $request)
    {
        $request->validate([
            'phone_number' => ['required', 'string', 'max:20'],
        ]);

        // Query tracking via your true structural column name
        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No matching resident account found.'
            ], 404);
        }

        // Mock verification state return code for high-fidelity presentation simulation
        $mockOtp = '123456';

        return response()->json([
            'success'   => true,
            'message'   => 'Account located. Verification OTP code generated cleanly.',
            'debug_otp' => $mockOtp // Sent back directly so you can display it in your test simulation terminal
        ], 200);
    }

    /**
     * 3. VERIFY CODE & LOGIN
     * Matches the mobile interface: "Enter Code" verification screen.
     */
    public function verifyOtp(Request $request)
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string'],
            'otp_code'     => ['required', 'string', 'size:6'],
        ]);

        // Presentation Verification Check: Intercepting the mock demonstration code sequence
        if ($validated['otp_code'] !== '123456') {
            return response()->json([
                'success' => false,
                'message' => 'The authentication verification code is invalid or has expired.'
            ], 422);
        }

        // Verify the user profile tracking integrity rules
        $user = User::where('phone_number', $validated['phone_number'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication protocol rejected. User record missing.'
            ], 404);
        }

        // Issue a clean state validation token authorization string via Laravel Sanctum
        $token = $user->createToken('mobile_access_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Identity verified successfully. Session authorized.',
            'token'   => $token,
            'user'    => [
                'full_name' => $user->full_name,
                'role'      => $user->role,
            ]
        ], 200);
    }
}