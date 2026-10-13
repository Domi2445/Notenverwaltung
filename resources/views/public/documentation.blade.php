@include('public.layout', ['title' => 'Dokumentation — Nexora'])
<main class="public-doc">
    <p class="eyebrow">Öffentlich</p>
    <h1>Dokumentation</h1>
    <p class="doc-lead">Der praktische Einstieg für Organisationen, Betreiber und Entwickler. Nexora bleibt ein schlanker Kern; Funktionen kommen als Module dazu.</p>

    <div class="doc-grid">
        <aside class="doc-nav">
            <a href="#start">Schnellstart</a>
            <a href="#navigation">Bereiche</a>
            <a href="#organisationen">Organisationen</a>
            <a href="#rollen">Rollen &amp; Zugriff</a>
            <a href="#plugins">Plugins entwickeln</a>
            <a href="#veroeffentlichen">Veröffentlichen</a>
            <a href="#preise">Preise</a>
            <a href="#betrieb">Betrieb</a>
            <a href="#support">Support</a>
        </aside>

        <article class="doc-content">
            <section id="start">
                <p class="eyebrow">01 · Schnellstart</p>
                <h2>Nexora kennenlernen</h2>
                <p>Die <a href="{{ route('demo') }}">öffentliche Demo</a> erklärt die Plattform. Mit <a href="{{ route('demo.start') }}">Demo starten</a> öffnest du einen isolierten Demo-Account und wirst direkt in den Kundenbereich weitergeleitet. Es ist keine Registrierung und keine Zahlungsangabe nötig.</p>
                <p>Für eine eigene Organisation nutzt du den <a href="{{ route('login') }}">Login</a>. Nach der Anmeldung führt der Kundenbereich zu den gebuchten Modulen und zur organisationsbezogenen Übersicht.</p>
            </section>

            <section id="navigation">
                <p class="eyebrow">02 · Orientierung</p>
                <h2>Die wichtigsten Bereiche</h2>
                <p>Die öffentliche Navigation und die geschützte Arbeitsoberfläche sind getrennt. Diese Adressen sind bereits vorhanden:</p>
                <pre><code>/demo             Öffentliche Produktseite
/dokumentation    Diese Dokumentation
/login            Kunden-Login
/kundenbereich    Geschützter Arbeitsbereich
/notenverwaltung  Erstes integriertes Modul
/module           Modulübersicht
/support          Support und Kontakt
/team             Team und Rollen
/einstellungen   Organisationseinstellungen
/admin/kunden     Betreiberübersicht für Plattformadministratoren</code></pre>
                <p>Rechtliche Informationen findest du unter <a href="{{ route('imprint') }}">Impressum</a> und <a href="{{ route('privacy') }}">Datenschutz</a>.</p>
            </section>

            <section id="organisationen">
                <p class="eyebrow">03 · Mandanten</p>
                <h2>Organisationen bleiben getrennt</h2>
                <p>Jeder Kunde arbeitet in einer eigenen <code>Organization</code>. Benutzer werden über eine Mitgliedschaft zugeordnet; Abos und Modulzugriffe gehören ebenfalls zu dieser Organisation. Ein Benutzer sieht niemals automatisch Daten eines anderen Kunden.</p>
                <p>Bei jedem neuen Modul gilt: Fachliche Tabellen erhalten eine <code>organization_id</code>, Abfragen werden auf die aktive Organisation eingeschränkt und eine Organisations-ID aus dem Browser wird nie ungeprüft übernommen.</p>
            </section>

            <section id="rollen">
                <p class="eyebrow">04 · Zugriff</p>
                <h2>Rollen, Team und Betreiber</h2>
                <p>Teammitglieder können mit den Rollen <code>administrator</code>, <code>editor</code> oder <code>viewer</code> vorgemerkt werden. Die Rolle liegt an der Organisationsmitgliedschaft und muss bei jeder schreibenden Aktion geprüft werden.</p>
                <p>Der Betreiberbereich <code>/admin/kunden</code> ist ausschließlich für Plattformadministratoren bestimmt. Dort werden nur Kundenorganisationen, Benutzeranzahl und aktive Abos angezeigt — nicht die fachlichen Inhalte der Kunden.</p>
            </section>

            <section id="plugins">
                <p class="eyebrow">05 · Erweiterungen</p>
                <h2>Plugins entwickeln</h2>
                <p>Ein Plugin implementiert den Nexora-Contract und kann eigene Modelle, Migrationen, Routen, Views, Events, Jobs und Berechtigungen mitbringen. Der Kern bleibt unverändert und spricht Module über stabile Contracts an.</p>
                <pre><code>use App\Plugins\Plugin;

final class CatalogPlugin implements Plugin
{
    public function id(): string { return 'vendor.catalog'; }
    public function name(): string { return 'Katalog'; }
    public function version(): string { return '1.0.0'; }
    public function description(): string { return 'Produkte verwalten.'; }
    public function icon(): string { return '◇'; }
    public function accent(): string { return 'secondary'; }
    public function url(): string { return route('catalog.index'); }
}</code></pre>
                <p>Registriert wird das Manifest in <code>config/plugins.php</code>. Für die technische Referenz mit Paketstruktur und Sicherheitsregeln siehe <a href="https://github.com/Domi2445/Notenverwaltung/blob/main/docs/PLUGIN-DEVELOPMENT.md">Plugin Development Guide</a>.</p>
                <pre><code>php artisan platform:plugins</code></pre>
            </section>

            <section id="veroeffentlichen">
                <p class="eyebrow">06 · Marktplatz</p>
                <h2>Module sicher veröffentlichen</h2>
                <p>Verteile Module als Composer-Paket mit eigenem Namespace und Service Provider. Über das <a href="{{ route('developer') }}">Entwicklerportal</a> reichst du die Paketdaten zur technischen Prüfung ein. Migrationen, Views und Routen gehören in das Paket; Nexora-Core-Dateien dürfen bei der Installation nicht überschrieben werden.</p>
                <p>Vor der Veröffentlichung dokumentierst du Berechtigungen, Datenaufbewahrung, benötigte PHP-Erweiterungen, Migrationen, Upgradepfad und Supportkontakt. Prüfe dein Paket mit:</p>
                <pre><code>composer validate
php artisan test
php artisan platform:plugins
npm run build</code></pre>
            </section>

            <section id="preise">
                <p class="eyebrow">07 · Geschäftsmodell</p>
                <h2>Cloud, Self-hosted und Modulpreise</h2>
                <p><strong>Nexora Cloud kostet {{ config('pricing.cloud_monthly_eur') }} € pro Monat.</strong> Infrastruktur, Updates und Backups laufen über Nexora.</p>
                <p><strong>Self-hosted kostet {{ config('pricing.self_hosted.base_one_time_eur') }} € einmalig.</strong> Ab {{ config('pricing.self_hosted.customer_threshold') }} Kunden sinkt die Lizenz auf {{ config('pricing.self_hosted.threshold_one_time_eur') }} €. Betrieb, Backups und Updates liegen dann beim Kunden.</p>
                <p>Entwickler können für ihr Modul einen Festpreis (<code>one_time</code>) oder ein laufendes Abo (<code>subscription</code>) anbieten. Für über Nexora vermittelte Modulumsätze fällt eine Servicegebühr von {{ config('pricing.developer_revenue_share_percent') }} % an — nur bei tatsächlichem Umsatz.</p>
            </section>

            <section id="betrieb">
                <p class="eyebrow">08 · Installation</p>
                <h2>Lokale Entwicklung und Produktion</h2>
                <p>Voraussetzungen sind PHP 8.4+, Composer, Node.js und npm. Der lokale Start:</p>
                <pre><code>composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve</code></pre>
                <p>Vor dem Livegang müssen <code>APP_DEBUG=false</code>, HTTPS, SMTP, Backups, Queue-Worker und ein Zahlungsanbieter eingerichtet werden. Abos, Webhooks, Rechnungen, Kündigungen und Auszahlungen sind aktuell noch nicht produktiv angebunden.</p>
            </section>

            <section id="support">
                <p class="eyebrow">09 · Hilfe</p>
                <h2>Support und Kontakt</h2>
                <p>Bei Fragen zur Plattform, zu Cloud-Betrieb oder Modulen erreichst du Nexora unter <a href="mailto:nexora@austit.de">nexora@austit.de</a>. Für rechtliche Fragen nutze bitte die Angaben im <a href="{{ route('imprint') }}">Impressum</a>.</p>
                <p class="legal-note">Die Dokumentation beschreibt den aktuellen Entwicklungsstand. Preise, Zahlungsabwicklung und rechtliche Texte müssen vor einem produktiven Verkauf final geprüft und ergänzt werden.</p>
            </section>
        </article>
    </div>
</main>
@include('public.footer')
