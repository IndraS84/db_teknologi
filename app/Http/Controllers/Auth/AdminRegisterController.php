<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminRegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:admin');
    }

    /**
     * Show the admin registration form.
     */
    public function showRegistrationForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.admin.register');
    }

    /**
     * Handle registration for new admin.
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'nama_admin' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:admins,username',
            'email' => 'required|email|max:255|unique:admins,email',
        ]);

        $admin = Admin::create([
            'nama_admin' => $data['nama_admin'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make('12345'), // Default password
        ]);

        // Log the admin in
        Auth::guard('admin')->login($admin);

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }
}
