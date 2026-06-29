<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (session('staff_id')) {
            return redirect()->route('do.index');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $staff = Staff::where('Email', $credentials['email'])->first();

        // NOTE: the seed data in ktm_edois.sql stores plain-text passwords
        // (e.g. "staff123") rather than bcrypt hashes, so we compare directly
        // here to match the prototype data. If your team later seeds hashed
        // passwords with Hash::make(), swap this line for:
        //   Hash::check($credentials['password'], $staff->Password_Hash)
        if (! $staff || $staff->Password_Hash !== $credentials['password']) {
            return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
        }

        if ($staff->Status !== 'Active') {
            return back()->withErrors(['email' => 'This account is inactive.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $request->session()->put('staff_id', $staff->User_ID);
        $request->session()->put('staff_name', $staff->Username);
        $request->session()->put('staff_role', $staff->Role);

        $staff->Last_Login = now();
        $staff->save();

        return redirect()->intended(route('do.index'));
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login');
    }
}
