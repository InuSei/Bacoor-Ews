<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CDRRMO Bacoor Flood EWS</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8 border border-gray-200">
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo.png') }}" alt="CDRRMO Bacoor Logo" class="w-20 h-20 mx-auto mb-3 object-contain drop-shadow-md">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">CDRRMO Bacoor</h1>
            <p class="text-xs text-gray-500 font-medium mt-1">Disaster Risk Reduction and Management Office</p>
        </div>

        <div class="flex bg-gray-100 rounded-xl p-1 mb-6">
            <button id="userTab" onclick="setRole('resident')" class="flex-1 py-2.5 text-sm font-bold rounded-lg transition-all text-white bg-blue-600 shadow">
                👤 User Login
            </button>
            <button id="adminTab" onclick="setRole('admin')" class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all text-gray-600 hover:text-gray-900">
                🛡️ Admin
            </button>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm rounded-xl font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
            @csrf 
            <input type="hidden" name="role" id="roleInput" value="resident">

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Username or Email</label>
                <input type="text" name="username" required placeholder="Enter your credentials" 
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Password</label>
                <input type="password" name="password" required placeholder="••••••••" 
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
            </div>

            <div class="flex justify-between items-center text-xs">
                <div id="mobileAuthLinkContainer">
                    <a href="{{ route('resident.find-account') }}" class="font-bold text-blue-600 hover:underline">
                        Login via Mobile OTP
                    </a>
                </div>
                <div class="text-right flex-1">
                    <a href="{{ route('resident.forgot-password') ?? '#' }}" class="font-semibold text-gray-500 hover:underline">Forgot password?</a>
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm transition-all shadow-lg shadow-blue-100">
                Log In
            </button>
        </form>

        <div class="mt-8 text-center border-t border-gray-100 pt-6">
            <p class="text-xs text-gray-500">
                Don't have an account yet? 
                <a href="{{ route('resident.register') }}" class="text-blue-600 font-bold hover:underline">Sign up</a>
            </p>
            <p class="text-[10px] text-gray-400 mt-4 tracking-wide uppercase">Restricted Access • City Government of Bacoor</p>
        </div>
    </div>

    <script>
        function setRole(role) {
            document.getElementById('roleInput').value = role;
            const userTab = document.getElementById('userTab');
            const adminTab = document.getElementById('adminTab');
            const mobileLink = document.getElementById('mobileAuthLinkContainer');

            if (role === 'resident') {
                userTab.className = "flex-1 py-2.5 text-sm font-bold rounded-lg transition-all text-white bg-blue-600 shadow";
                adminTab.className = "flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all text-gray-600 hover:text-gray-900";
                
                mobileLink.classList.remove('hidden');
            } else {
                adminTab.className = "flex-1 py-2.5 text-sm font-bold rounded-lg transition-all text-white bg-blue-600 shadow";
                userTab.className = "flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all text-gray-600 hover:text-gray-900";
                
                mobileLink.classList.add('hidden');
            }
        }
    </script>
</body>
</html>