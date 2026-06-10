<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Account via Email - CDRRMO Bacoor</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center font-sans p-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-gray-200 overflow-hidden flex flex-col justify-between relative min-h-[600px]">
        
        <div class="p-8 flex flex-col items-center text-center pt-12">
            <img src="{{ asset('images/logo.png') }}" alt="CDRRMO Bacoor Logo" class="w-20 h-20 object-contain drop-shadow-md mb-3">
            <h1 class="text-xl font-black text-slate-900 uppercase tracking-tight">CDRRMO Bacoor</h1>
            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Risk Reduction Management</p>
        </div>

        <div class="px-8 flex-1 flex flex-col justify-center">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Find your account</h2>
                <p class="text-xs text-gray-500 mt-1 font-semibold">Enter your email address.</p>
            </div>

            <form method="POST" action="{{ route('resident.find-account-email.post') }}" class="space-y-4">
                @csrf
                
                <div>
                    <input 
                        type="email" 
                        name="email" 
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="example@domain.com" 
                        class="w-full h-12 px-4 rounded-xl border {{ $errors->has('email') ? 'border-red-500 bg-red-50' : 'border-gray-200 bg-gray-50' }} text-slate-900 text-sm font-bold focus:outline-none focus:border-blue-500 focus:bg-white transition-all tracking-wide"
                        required
                    >
                    
                    @error('email')
                        <p class="text-red-600 text-[11px] font-bold mt-1.5 px-1 animate-pulse">⚠️ {{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-3 pt-4">
                    <button type="submit" class="w-full h-12 bg-[#007FFF] hover:bg-[#0066CC] text-white font-bold text-sm rounded-xl shadow-md transition-all cursor-pointer flex items-center justify-center">
                        Continue
                    </button>

                    <a href="{{ route('login') }}" class="w-full h-12 bg-white hover:bg-gray-50 text-slate-700 font-bold text-sm rounded-xl border border-gray-200 shadow-sm transition-all flex items-center justify-center text-decoration-none">
                        Cancel
                    </a>
                </div>
            </form>

            <div class="text-center mt-6">
                <a href="{{ route('resident.find-account') }}" class="text-xs font-bold text-slate-700 hover:text-blue-600 transition-colors inline-block text-decoration-none">
                    Search by mobile number instead
                </a>
            </div>
        </div>

        <div class="p-6 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider border-t border-gray-100 bg-white mt-8">
            Restricted Access • City Government of Bacoor
        </div>

    </div>

</body>
</html>