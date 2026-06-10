<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code - CDRRMO Bacoor</title>
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
            <div class="mb-6 flex justify-between items-end">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Enter Code</h2>
                </div>
                <a href="{{ route('resident.find-account') }}" class="text-xs font-bold text-blue-600 hover:underline">
                    Change Number
                </a>
            </div>

            <form method="POST" action="{{ route('resident.verify-otp.post') }}" class="space-y-4">
                @csrf
                
                <input type="hidden" name="phone_number" value="{{ session('auth_phone') }}">

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">One-Time Password</label>
                    <input 
                        type="text" 
                        name="otp_code" 
                        id="otp_code"
                        maxlength="6"
                        placeholder="123456" 
                        class="w-full h-12 px-4 rounded-xl border tracking-widest text-center {{ $errors->has('otp_code') ? 'border-red-500 bg-red-50' : 'border-gray-300 bg-white' }} text-slate-900 text-lg font-black focus:outline-none focus:border-blue-500 shadow-2xs transition-all"
                        required
                    >
                    
                    @error('otp_code')
                        <p class="text-red-600 text-[11px] font-bold mt-1.5 text-center animate-pulse">⚠️ {{ $message }}</p>
                    @enderror
                </div>

                <div class="text-center">
                    <p id="countdownLabel" class="text-xs text-gray-400 font-bold uppercase tracking-wide">
                        Resend code in <span id="timerSeconds">24</span>s
                    </p>
                </div>

                <div class="space-y-2 pt-4">
                    <button type="submit" class="w-full h-12 bg-[#007FFF] hover:bg-[#0066CC] text-white font-bold text-sm rounded-xl shadow-md transition-all cursor-pointer flex items-center justify-center">
                        Verify & Login
                    </button>
                </div>
            </form>
        </div>

        <div class="p-6 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider border-t border-gray-200 bg-gray-50">
            Restricted Access • City Government of Bacoor
        </div>

    </div>

    <script>
        let duration = 24;
        const timerSecondsElement = document.getElementById('timerSeconds');
        const countdownLabelElement = document.getElementById('countdownLabel');

        const intervalClock = setInterval(() => {
            duration--;
            if (duration <= 0) {
                clearInterval(intervalClock);
                countdownLabelElement.innerHTML = `<button onclick="window.location.reload()" class="text-xs font-black text-blue-600 hover:underline bg-transparent border-none cursor-pointer uppercase tracking-wider">Resend OTP Code</button>`;
            } else {
                timerSecondsElement.innerText = duration;
            }
        }, 1000);
    </script>

</body>
</html>