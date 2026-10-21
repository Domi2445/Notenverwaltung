<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="color-scheme" content="light dark">
        <title>@yield('title') — {{ config('app.name') }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="nv">
        <div class="auth">
            <aside class="auth-aside">
                <a class="nv-brand" href="/">
                    <span class="nv-brand-mark">N</span>
                    <span class="nv-brand-text">
                        <strong>{{ config('app.name') }}</strong>
                        <small>Schulportal</small>
                    </span>
                </a>

                <div>
                    <h2>Noten, Klassen und Stundenpläne an einem Ort.</h2>
                    <p>Die Schulverwaltung für Lehrkräfte: übersichtlich, schnell und an einem Ort.</p>
                </div>

                <ul class="auth-features">
                    <li><span><x-icon name="star" size="16" /></span> Noten erfassen und Notenspiegel auswerten</li>
                    <li><span><x-icon name="calendar" size="16" /></span> Stundenpläne pro Klasse und Lehrkraft</li>
                    <li><span><x-icon name="clipboard" size="16" /></span> Klassenarbeiten planen und nachverfolgen</li>
                </ul>
            </aside>

            <main class="auth-main">
                <div class="auth-card">
                    <a class="nv-brand" href="/">
                        <span class="nv-brand-mark">N</span>
                        <span class="nv-brand-text">
                            <strong>{{ config('app.name') }}</strong>
                            <small>Schulportal</small>
                        </span>
                    </a>

                    <div>
                        <p class="eyebrow">@yield('eyebrow')</p>
                        <h1>@yield('heading')</h1>
                        <p class="lead">@yield('lead')</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-error" role="alert">
                            <x-icon name="x" />
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </body>
</html>
