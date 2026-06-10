<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (Auth::user()->role === 'resident') {
            return redirect()->route('resident.map-dashboard');
        }

        $totalUsers = \App\Models\User::count();
        return view('dashboard', compact('totalUsers'));
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'resident') {
                return redirect()->route('resident.map-dashboard');
            }
            return redirect()->route('dashboard');
        }

        return view('login'); 
    }

    // 🌟 FIX: Removed the login processing code. This controller only handles logging out now!
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}