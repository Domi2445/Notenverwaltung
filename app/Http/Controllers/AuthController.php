<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    // GET /login – Route "login" (siehe docs/PLAN.md, Schritt 3)
    public function showLogin()
    {
        // TODO: Die View "auth.login" zurückgeben -> return view('...');
    }

    // POST /login – Route "login.store"
    public function login(Request $request)
    {
        // TODO:
        // 1. Eingaben prüfen: email (Pflicht, E-Mail-Format) und password (Pflicht)
        //    -> $credentials = $request->validate([...]);
        // 2. Anmelden versuchen -> Auth::attempt($credentials, $request->boolean('remember'))
        //    "remember" ist die Checkbox "Angemeldet bleiben".
        // 3. Wenn das fehlschlägt: zurück zum Formular mit Fehlermeldung und der alten E-Mail
        //    -> back()->withErrors(['email' => '...'])->onlyInput('email')
        // 4. Bei Erfolg: Session erneuern (Schutz vor Session-Fixation)
        //    -> $request->session()->regenerate();
        // 5. Zur Übersicht weiterleiten -> redirect()->intended(route('dashboard'))
        //
        // Nicht vergessen: oben "use Illuminate\Support\Facades\Auth;" ergänzen
    }

    // GET /registrieren – Route "register"
    public function showRegister()
    {
        // TODO: Die View "auth.register" zurückgeben
    }

    // POST /registrieren – Route "register.store"
    public function register(Request $request)
    {
        // TODO:
        // 1. Eingaben prüfen:
        //    - name: Pflicht, Text, max. 120 Zeichen
        //    - email: Pflicht, E-Mail-Format, darf es noch nicht geben -> 'unique:users,email'
        //    - password: Pflicht, mind. 8 Zeichen, muss mit password_confirmation übereinstimmen -> 'confirmed'
        // 2. Benutzer anlegen -> User::create([...])
        //    Das Passwort wird automatisch verschlüsselt, weil im User-Model 'password' => 'hashed' gecastet ist.
        // 3. Direkt anmelden -> Auth::login($user);
        // 4. Session erneuern und zur Übersicht weiterleiten
    }

    // POST /logout – Route "logout"
    public function logout(Request $request)
    {
        // TODO:
        // 1. Abmelden -> Auth::logout();
        // 2. Session ungültig machen und CSRF-Token erneuern
        //    -> $request->session()->invalidate();  und  $request->session()->regenerateToken();
        // 3. Zur Login-Seite weiterleiten
    }
}
