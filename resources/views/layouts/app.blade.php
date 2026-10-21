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
        <div class="nv-app">
            @include('partials.sidebar')

            <div class="nv-main">
                <header class="nv-topbar">
                    <div class="nv-org">
                        <x-icon name="building" size="16" />
                        <span>{{ config('app.school_name') }}</span>
                    </div>
                    <span class="nv-date">{{ now()->locale('de')->isoFormat('dddd, D. MMMM YYYY') }}</span>
                </header>

                <main class="nv-content">
                    <div class="page-header">
                        <div>
                            <p class="eyebrow">@yield('eyebrow')</p>
                            <h1>@yield('heading')</h1>
                            @hasSection('lead')
                                <p class="lead">@yield('lead')</p>
                            @endif
                        </div>
                        @hasSection('actions')
                            <div class="page-actions">@yield('actions')</div>
                        @endif
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success" role="status">
                            <x-icon name="check-circle" />
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-error" role="alert">
                            <x-icon name="x" />
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
