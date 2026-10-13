<!DOCTYPE html>
<html lang="de">
    <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Support — Nexora</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
    <body>
        <div class="platform-page">
            <header class="module-header"><a class="back-link" href="/">← Nexora</a><span class="module-version">Hilfe & Support</span></header>
            <main class="platform-main">
                <p class="eyebrow">Wir sind für dich da</p><h1>Wie können wir helfen?</h1>
                <p class="module-lead">Nexora bleibt offen, verständlich und erreichbar. Schreib uns, wenn etwas unklar ist oder du eine Idee für die Plattform hast.</p>
                <div class="platform-card-grid">
                    <a class="platform-card platform-card-coral" href="mailto:nexora@austit.de"><span class="platform-card-icon">↗</span><h2>E-Mail schreiben</h2><p>nexora@austit.de</p><small>Antwort innerhalb eines Werktags</small></a>
                    <a class="platform-card platform-card-mint" href="{{ route('marketplace') }}"><span class="platform-card-icon">◇</span><h2>Module entdecken</h2><p>Erweiterungen für deinen Arbeitsbereich</p><small>Zum Modul-Marktplatz →</small></a>
                    <a class="platform-card platform-card-outline" href="{{ route('developer') }}"><span class="platform-card-icon">＋</span><h2>Für Entwickler</h2><p>Plugin einreichen und prüfen lassen</p><small>Zum Entwicklerportal →</small></a>
                </div>
                <section class="platform-note"><p class="eyebrow">Unser Grundsatz</p><h2>Du entscheidest über den Betrieb.</h2><p>Nexora Cloud kostet {{ config('pricing.cloud_monthly_eur') }} € pro Monat über unsere Infrastruktur. Die Self-hosted-Lizenz kostet {{ config('pricing.self_hosted.base_one_time_eur') }} € einmalig und sinkt ab {{ config('pricing.self_hosted.customer_threshold') }} Kunden auf {{ config('pricing.self_hosted.threshold_one_time_eur') }} €. Entwickler zahlen nur bei Modulumsatz eine Servicegebühr von {{ config('pricing.developer_revenue_share_percent') }} %.</p></section>
            </main>
        </div>
    </body>
</html>
