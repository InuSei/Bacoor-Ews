<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - CDRRMO Bacoor Flood EWS</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl p-8 border border-gray-200 my-8">
        
        <div class="text-center mb-6">
            <img src="{{ asset('images/logo.png') }}" alt="CDRRMO Bacoor Logo" class="w-20 h-20 mx-auto mb-3 object-contain drop-shadow-md">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Create Account</h1>
            <p class="text-xs text-gray-500 font-medium mt-1">Register for Flood Early Warning Alerts</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-3 bg-red-50 border border-red-200 text-red-600 text-xs rounded-xl font-medium">
                Please check the form below for errors before continuing.
            </div>
        @endif

        <form method="POST" action="{{ route('resident.register.post') }}" class="space-y-4">
            @csrf 

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Full Name</label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" required placeholder="Juan Dela Cruz" 
                       class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('full_name') ? 'border-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                @error('full_name')
                    <p class="text-red-500 text-[10px] font-bold mt-1 pl-1">⚠️ {{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" required placeholder="choose_a_username" 
                       class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('username') ? 'border-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                @error('username')
                    <p class="text-red-500 text-[10px] font-bold mt-1 pl-1">⚠️ {{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Mobile Number</label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" required placeholder="09XX XXX XXXX" 
                       class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('phone_number') ? 'border-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-all tracking-wide">
                @error('phone_number')
                    <p class="text-red-500 text-[10px] font-bold mt-1 pl-1">⚠️ {{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="example@domain.com" 
                       class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                @error('email')
                    <p class="text-red-500 text-[10px] font-bold mt-1 pl-1">⚠️ {{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Select Barangay</label>
                
                <input type="hidden" name="barangay" id="hidden_barangay" required>
                
                <div class="relative" id="barangayDropdownWrapper">
                    <div onclick="toggleBarangayDropdown()" class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('barangay') ? 'border-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:border-blue-500 hover:bg-white transition-all font-bold text-slate-700 cursor-pointer flex justify-between items-center select-none">
                        <span id="selectedBarangayText" class="text-gray-400">Choose your area...</span>
                        <span class="text-[10px] text-gray-500">▼</span>
                    </div>

                    <div id="barangayMenuOptions" class="hidden absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-xl shadow-2xl max-h-[220px] overflow-y-auto">
                        <ul class="text-sm font-bold text-slate-700 divide-y divide-gray-100">
                            <li onclick="selectBarangay('Brgy. Bayanan', 'Brgy. Bayanan (CDRRMO HQ)')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors flex items-center gap-2">
                                <span class="text-blue-600">🏢</span> Brgy. Bayanan (CDRRMO HQ)
                            </li>
                            <li onclick="selectBarangay('Brgy. Mambog I', 'Brgy. Mambog I')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors">🏠 Brgy. Mambog I</li>
                            <li onclick="selectBarangay('Brgy. Mambog II', 'Brgy. Mambog II')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors">🏠 Brgy. Mambog II</li>
                            <li onclick="selectBarangay('Brgy. Mambog III', 'Brgy. Mambog III')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors">🏠 Brgy. Mambog III</li>
                            <li onclick="selectBarangay('Brgy. Habay I', 'Brgy. Habay I')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors">🏠 Brgy. Habay I</li>
                            <li onclick="selectBarangay('Brgy. Habay II', 'Brgy. Habay II')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors">🏠 Brgy. Habay II</li>
                            <li onclick="selectBarangay('Brgy. Ligas I', 'Brgy. Ligas I')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors">🏠 Brgy. Ligas I</li>
                            <li onclick="selectBarangay('Brgy. Ligas II', 'Brgy. Ligas II')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors">🏠 Brgy. Ligas II</li>
                            <li onclick="selectBarangay('Brgy. San Nicolas I', 'Brgy. San Nicolas I')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors">🏠 Brgy. San Nicolas I</li>
                            <li onclick="selectBarangay('Brgy. San Nicolas II', 'Brgy. San Nicolas II')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors">🏠 Brgy. San Nicolas II</li>
                            <li onclick="selectBarangay('Brgy. Niog I', 'Brgy. Niog I')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors">🏠 Brgy. Niog I</li>
                            <li onclick="selectBarangay('Brgy. Panapaan I', 'Brgy. Panapaan I')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors">🏠 Brgy. Panapaan I</li>
                            <li onclick="selectBarangay('Brgy. Talaba II', 'Brgy. Talaba II')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors">🏠 Brgy. Talaba II</li>
                            <li onclick="selectBarangay('Brgy. Molino I', 'Brgy. Molino I')" class="px-4 py-3 hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition-colors">🏠 Brgy. Molino I</li>
                        </ul>
                    </div>
                </div>
                @error('barangay')
                    <p class="text-red-500 text-[10px] font-bold mt-1 pl-1">⚠️ Please select your Barangay.</p>
                @enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Password</label>
                <input type="password" name="password" required placeholder="Create a secure password" 
                       class="w-full px-4 py-3 bg-gray-50 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
                @error('password')
                    <p class="text-red-500 text-[10px] font-bold mt-1 pl-1">⚠️ {{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[11px] font-bold text-gray-700 uppercase tracking-wider mb-1.5">Confirm Password</label>
                <input type="password" name="password_confirmation" required placeholder="Type your password again" 
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm transition-all shadow-lg shadow-blue-100 cursor-pointer">
                    Register Account
                </button>
            </div>
        </form>

        <div class="mt-8 text-center border-t border-gray-100 pt-6">
            <p class="text-xs text-gray-500">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-blue-600 font-bold hover:underline transition-colors">Log In</a>
            </p>
            <p class="text-[10px] text-gray-400 mt-4 tracking-wide uppercase">CDRRMO Bacoor • Data Privacy Secured</p>
        </div>
        
    </div>

    <script>
        function toggleBarangayDropdown() {
            const menu = document.getElementById('barangayMenuOptions');
            menu.classList.toggle('hidden');
        }

        function selectBarangay(dbValue, displayValue) {
            document.getElementById('hidden_barangay').value = dbValue; 
            
            const displayText = document.getElementById('selectedBarangayText');
            displayText.innerText = displayValue;
            displayText.classList.remove('text-gray-400');
            displayText.classList.add('text-slate-800');

            document.getElementById('barangayMenuOptions').classList.add('hidden');
        }

        window.addEventListener('click', function(event) {
            const wrapper = document.getElementById('barangayDropdownWrapper');
            if (!wrapper.contains(event.target)) {
                document.getElementById('barangayMenuOptions').classList.add('hidden');
            }
        });
    </script>
</body>
</html>