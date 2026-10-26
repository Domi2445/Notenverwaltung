<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // GET /login – Route "login"
    public function showLogin()
    {
        return view('auth.login');
    }

    // POST /login – Route "login.store"
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Falsche E-Mail oder Passwort'])->onlyInput('email');
    }

    // GET /registrieren – Route "register"
    // Registrieren geht nur, solange es noch keinen Benutzer gibt.
    // Alle weiteren Logins legt der Admin über die Oberfläche an.
    public function showRegister()
    {
        if (User::exists()) {
            return redirect()->route('login');
        }

        return view('auth.register');
    }

    // POST /registrieren – Route "register.store"
    public function register(Request $request)
    {
        if (User::exists()) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        // Der allererste Benutzer wird Admin
        $validated['role'] = 'admin';

        $user = User::create($validated);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    // POST /logout – Route "logout"
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
