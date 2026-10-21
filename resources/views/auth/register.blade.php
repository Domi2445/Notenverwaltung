@extends('auth.layout')

@section('title', 'Registrieren')
@section('eyebrow', 'Neues Konto')
@section('heading', 'Starte mit der Notenverwaltung.')
@section('lead', 'Erstelle deinen Zugang und melde dich danach direkt an.')

@section('content')
    <form method="POST" action="{{ route('register.store') }}">
        @csrf
        <label class="field">
            <span>Name</span>
            <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus>
        </label>
        <label class="field">
            <span>E-Mail</span>
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
        </label>
        <label class="field">
            <span>Passwort</span>
            <input type="password" name="password" autocomplete="new-password" minlength="8" required>
        </label>
        <label class="field">
            <span>Passwort wiederholen</span>
            <input type="password" name="password_confirmation" autocomplete="new-password" required>
        </label>
        <button type="submit" class="btn btn-primary btn-block">Konto erstellen</button>
    </form>

    <div class="auth-links">
        <span>Bereits registriert? <a href="{{ route('login') }}">Anmelden</a></span>
    </div>
@endsection
