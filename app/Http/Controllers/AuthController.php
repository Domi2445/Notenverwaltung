<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    // GET /login – Route "login" (siehe docs/PLAN.md, Schritt 3)
    public function showLogin()
    {

        return view('auth.login');
    }

    // POST /login – Route "login.store"
    public function login(Request $request)
    {

        if($credentials = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ])) {
            if(Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended(route('dashboard'));
            }
        }
        return back()->withErrors(['email' => 'Falsche E-Mail oder Passwort'])->onlyInput('email');

    }

    // GET /registrieren – Route "register"
    public function showRegister()
    {


        return view('auth.register');
    }

    // POST /registrieren – Route "register.store"
    public function register(Request $request)
    {



        if($request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ])) {
            $user = User::create($request->all());
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }
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
