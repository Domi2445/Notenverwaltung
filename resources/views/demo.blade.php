<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Nexora ist die modulare Plattform für individuelle Organisationen und Prozesse.">
        <title>Nexora — Die Plattform, die sich anpasst</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="demo-page">
            <header class="demo-nav">
                <a class="brand demo-brand" href="{{ route('demo') }}"><span class="brand-mark">N</span><strong>Nexora</strong></a>
                <nav class="public-nav"><a href="#plattform">Plattform</a><a href="#entwickler">Entwickler</a><a href="{{ route('documentation') }}">Dokumentation</a></nav>
                <div class="public-actions"><a class="demo-login" href="{{ route('login') }}">Anmelden</a><a class="demo-button" href="#demo">Demo ansehen</a></div>
            </header>
            <main>
                <section class="landing-hero">
                    <div class="landing-hero-copy">
                        <p class="eyebrow">Modulare Software für echte Arbeit</p>
                        <h1>Eine Plattform.<br><em>Deine Möglichkeiten.</em></h1>
                        <p class="landing-lead">Nexora gibt Organisationen einen klaren digitalen Ort — und Entwicklern die Freiheit, genau die Module zu bauen, die gebraucht werden.</p>
                        <div class="hero-actions"><a class="primary-button" href="{{ route('demo.start') }}">Demo starten <span>→</span></a><a class="quiet-link" href="{{ route('documentation') }}">Wie Nexora funktioniert</a></div>
                    </div>
                    <div class="hero-visual"><div class="visual-window"><div class="visual-bar"><span></span><span></span><span></span><b>NEXORA / ÜBERSICHT</b></div><div class="visual-body"><div class="visual-sidebar"></div><div class="visual-lines"><i></i><i></i><i></i><div class="visual-cards"><span></span><span></span><span></span></div></div></div></div><div class="visual-badge">Deine Daten.<br><strong>Deine Kontrolle.</strong></div></div>
                </section>
                <section class="trust-line"><span>Für Organisationen, die mehr vorhaben.</span><span>Ein Kern. Viele Wege.</span><span>Offen für Entwickler.</span></section>
                <section class="landing-section" id="plattform"><div class="section-intro"><p class="eyebrow">Der Kern</p><h2>Weniger Grenzen.<br><em>Mehr Spielraum.</em></h2><p>Nexora bleibt schlank, damit du es an deinen Alltag anpassen kannst — nicht umgekehrt.</p></div><div class="feature-list"><div><b>01</b><span><strong>Module statt Kompromisse</strong><small>Notenverwaltung, CRM, Shop oder eigene Prozesse — installiere nur, was du wirklich brauchst.</small></span></div><div><b>02</b><span><strong>Deine Organisation bleibt deine</strong><small>Getrennte Arbeitsbereiche, klare Rollen und Daten, die nur dort sichtbar sind, wo sie hingehören.</small></span></div><div><b>03</b><span><strong>Gemeinsam wird es günstiger</strong><small>Cloud für {{ config('pricing.cloud_monthly_eur') }} € monatlich. Self-hosted ab {{ config('pricing.self_hosted.customer_threshold') }} Kunden nur noch {{ config('pricing.self_hosted.threshold_one_time_eur') }} € einmalig.</small></span></div></div></section>
                <section class="pricing-section"><p class="eyebrow">Einfaches Preismodell</p><h2>Du entscheidest, wo Nexora läuft.</h2><div class="pricing-options"><div><span class="pricing-label">Nexora Cloud</span><strong>{{ config('pricing.cloud_monthly_eur') }} € <small>/ Monat</small></strong><p>Wir betreiben Updates, Infrastruktur und Backups für dich.</p><a class="quiet-link" href="{{ route('support') }}">Cloud anfragen →</a></div><div><span class="pricing-label">Self-hosted</span><strong>{{ config('pricing.self_hosted.base_one_time_eur') }} € <small>einmalig</small></strong><p>Ab {{ config('pricing.self_hosted.customer_threshold') }} Kunden sinkt die Lizenz auf {{ config('pricing.self_hosted.threshold_one_time_eur') }} €.</p><a class="quiet-link" href="{{ route('documentation') }}">Technische Doku →</a></div></div></section>
                <section class="demo-callout" id="demo"><div><p class="eyebrow">Interaktive Vorschau</p><h2>Sieh dir Nexora<br>in Ruhe an.</h2><p>Keine Registrierung. Keine Zahlungsdaten. Ein isolierter Demo-Account.</p></div><a class="primary-button light-button" href="{{ route('demo.start') }}">Demo öffnen <span>→</span></a></section>
                <section class="landing-section developer-section" id="entwickler"><div class="section-intro"><p class="eyebrow">Für Entwickler</p><h2>Baue etwas,<br><em>das gebraucht wird.</em></h2></div><div class="developer-copy"><p>Nexora stellt den stabilen Kern und die Infrastruktur bereit. Für vermittelte Modulumsätze fällt eine Servicegebühr von {{ config('pricing.developer_revenue_share_percent') }} % an — transparent und nur bei Umsatz.</p><a class="quiet-link" href="{{ route('documentation') }}">Zur Entwicklerdokumentation →</a></div></section>
            </main>
            <footer class="public-footer"><div><a class="brand footer-brand" href="{{ route('demo') }}"><span class="brand-mark">N</span><strong>Nexora</strong></a><p>Eine Plattform für deine Prozesse.</p></div><div class="footer-links"><strong>Entdecken</strong><a href="{{ route('documentation') }}">Dokumentation</a><a href="{{ route('demo') }}#entwickler">Für Entwickler</a><a href="{{ route('support') }}">Support</a></div><div class="footer-links"><strong>Rechtliches</strong><a href="{{ route('imprint') }}">Impressum</a><a href="{{ route('privacy') }}">Datenschutz</a></div><div class="footer-contact"><strong>Kontakt</strong><a href="mailto:nexora@austit.de">nexora@austit.de</a></div><div class="footer-bottom">© {{ date('Y') }} Nexora <span>v1.0.0-alpha</span></div></footer>
        </div>
    </body>
</html>
