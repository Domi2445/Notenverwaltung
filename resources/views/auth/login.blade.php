@extends('auth.layout')

@section('title', 'Anmelden')
@section('eyebrow', 'Zugang für Lehrkräfte')
@section('heading', 'Willkommen zurück.')
@section('lead', 'Melde dich an, um Noten, Klassen und Stundenpläne deiner Schule zu verwalten.')

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

    <div class="auth-links">
        <span>Noch kein Konto? <a href="{{ route('register') }}">Jetzt registrieren</a></span>
    </div>
@endsection
