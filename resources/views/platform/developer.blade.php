<!DOCTYPE html>
<html lang="de">
    <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Für Entwickler — Nexora</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
    <body>
        <div class="platform-page">
            <header class="module-header"><a class="back-link" href="/">← Nexora</a><span class="module-version">Entwicklerportal</span></header>
            <main class="platform-main">
                <p class="eyebrow">Module veröffentlichen</p><h1>Deine Erweiterung auf Nexora.</h1>
                <p class="module-lead">Reiche dein Modul zur Prüfung ein. Nach der technischen Prüfung entscheidet Nexora über die Veröffentlichung im Marktplatz.</p>
                @if (session('status'))<div class="platform-note"><p>{{ session('status') }}</p></div>@endif
                @if ($errors->any())<div class="platform-note"><p>{{ $errors->first() }}</p></div>@endif
                <form class="platform-form" method="POST" action="{{ route('developer.submit') }}">
                    @csrf
                    <label>Entwickler / Unternehmen<input name="developer_name" value="{{ old('developer_name') }}" required maxlength="120"></label>
                    <label>E-Mail<input type="email" name="developer_email" value="{{ old('developer_email') }}" required maxlength="255"></label>
                    <label>Composer-Paketname<input name="package_name" value="{{ old('package_name') }}" placeholder="vendor/module" required maxlength="160"></label>
                    <label>Version<input name="version" value="{{ old('version', '1.0.0') }}" required maxlength="30"></label>
                    <label>Beschreibung<textarea name="description" required maxlength="2000">{{ old('description') }}</textarea></label>
                    <label>Preismodell<select name="plan_type"><option value="one_time">Festpreis</option><option value="subscription">Monatsabo</option></select></label>
                    <label>Preis in Euro<input type="number" name="price_eur" value="{{ old('price_eur') }}" min="0" step="0.01" required></label>
                    <button class="primary-button" type="submit">Modul zur Prüfung einreichen <span>→</span></button>
                </form>
            </main>
        </div>
    </body>
</html>
