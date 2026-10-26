@extends('auth.layout')

@section('title', 'Anmelden')
@section('eyebrow', 'Zugang für Lehrkräfte und Schüler')
@section('heading', 'Willkommen zurück.')
@section('lead', 'Melde dich mit den Zugangsdaten an, die du von der Schulverwaltung bekommen hast.')

@section('content')
    <form method="POST" action="{{ route('login.store') }}">
        @csrf
        <label class="field">
            <span>E-Mail</span>
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
        </label>
        <label class="field">
            <span>Passwort</span>
            <input type="password" name="password" autocomplete="current-password" required>
        </label>
        <label class="auth-check">
            <input type="checkbox" name="remember"> Angemeldet bleiben
        </label>
        <button type="submit" class="btn btn-primary btn-block">Anmelden</button>
    </form>

    {{-- Registrieren gibt es nur für den allerersten Benutzer (wird Admin) --}}
    @unless (\App\Models\User::exists())
        <div class="auth-links">
            <span>Noch kein Konto? <a href="{{ route('register') }}">Admin-Konto einrichten</a></span>
        </div>
    @endunless
@endsection
