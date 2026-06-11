<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - CDRRMO Bacoor</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-[#EAEAEA] min-h-screen flex items-center justify-center font-sans p-4">

    <div class="w-full max-w-[390px] min-h-[760px] bg-[#F3F4F6] rounded-[40px] shadow-2xl border-8 border-slate-800 flex flex-col justify-between overflow-hidden relative">
        
        <div class="p-8 flex flex-col items-center text-center pt-16">
            <img src="{{ asset('images/logo.png') }}" alt="CDRRMO Bacoor Logo" class="w-20 h-20 object-contain drop-shadow-md mb-3">
            <h1 class="text-md font-black text-slate-900 uppercase tracking-tight">CDRRMO Bacoor</h1>
            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Risk Reduction Management</p>
        </div>

        <div class="px-6 flex-1 flex flex-col justify-center">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Secure Reset</h2>
                <p class="text-xs text-gray-500 mt-1 font-semibold">Enter the OTP sent to your phone and your new password below.</p>
            </div>

            <form method="POST" action="{{ route('resident.reset-password.post') ?? '#' }}" class="space-y-4">
                @csrf
                
                <input type="hidden" name="phone_number" value="{{ session('auth_phone') }}">
                <input type="hidden" name="email" value="{{ session('auth_email') }}">

                <div>
                    <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">6-Digit Verification Code</label>
                    <input 
                        type="text" 
                        name="otp_code" 
                        maxlength="6"
                        placeholder="123456" 
                        class="w-full h-11 px-4 rounded-xl border tracking-widest text-center {{ $errors->has('otp_code') ? 'border-red-500 bg-red-50' : 'border-gray-300 bg-white' }} text-slate-900 text-lg font-black focus:outline-none focus:border-blue-500 shadow-2xs transition-all"
                        required
                    >
                    @error('otp_code')
                        <p class="text-red-600 text-[10px] font-bold mt-1 text-center animate-pulse">⚠️ {{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">New Password</label>
                    <input 
                        type="password" 
                        name="new_password" 
                        placeholder="••••••••" 
                        class="w-full h-11 px-4 rounded-xl border {{ $errors->has('new_password') ? 'border-red-500 bg-red-50' : 'border-gray-300 bg-white' }} text-slate-900 text-sm font-bold focus:outline-none focus:border-blue-500 shadow-2xs transition-all"
                        required
                    >
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Confirm Password</label>
                    <input 
                        type="password" 
                        name="new_password_confirmation" 
                        placeholder="••••••••" 
                        class="w-full h-11 px-4 rounded-xl border {{ $errors->has('new_password_confirmation') ? 'border-red-500 bg-red-50' : 'border-gray-300 bg-white' }} text-slate-900 text-sm font-bold focus:outline-none focus:border-blue-500 shadow-2xs transition-all"
                        required
                    >
                    @error('new_password')
                        <p class="text-red-600 text-[10px] font-bold mt-1 text-center animate-pulse">⚠️ {{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2 pt-4">
                    <button type="submit" class="w-full h-12 bg-[#007FFF] hover:bg-[#0066CC] text-white font-bold text-sm rounded-xl shadow-md transition-all cursor-pointer flex items-center justify-center">
                        Update Password & Login
                    </button>
                    
                    <a href="{{ route('login') }}" class="w-full h-12 bg-transparent hover:bg-gray-200 text-slate-600 font-bold text-sm rounded-xl transition-all flex items-center justify-center text-decoration-none">
                        Cancel Request
                    </a>
                </div>
            </form>
        </div>

        <div class="p-6 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider border-t border-gray-200 bg-gray-50">
            Restricted Access • City Government of Bacoor
        </div>

    </div>

</body>
</html>