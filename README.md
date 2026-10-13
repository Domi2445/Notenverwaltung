# Nexora

Nexora ist eine modulare Plattform für Organisationen. Fachfunktionen werden
als unabhängige Module installiert. Nexora Cloud kostet 15 € pro Monat;
Self-hosted kostet 50 € einmalig.
Ein Modul kann beispielsweise Notenverwaltung, CRM, Shop oder individuelle
Kundenprozesse abbilden.

## Bereiche

| Bereich | URL | Zweck |
| --- | --- | --- |
| Öffentliche Demo | `/demo` | Vorschau ohne Anmeldung |
| Login | `/login` | Anmeldung einer Kundenorganisation |
| Kundenbereich | `/kundenbereich` | Nur für eingeloggte Organisationsmitglieder |
| Modul-Marktplatz | `/module` | Installierte und verfügbare Module |
| Betreiberbereich | `/admin/kunden` | Nur für Plattformadministratoren |
| Support | `/support` | Kontakt zu `nexora@austit.de` |

## Lokale Entwicklung

Voraussetzungen:

- PHP 8.4+
- Composer
- Node.js und npm

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

Die Anwendung ist danach unter `http://127.0.0.1:8000` erreichbar. Die
öffentliche Startseite zeigt die Demo. Der Entwicklungsserver mit
automatischem Asset-Rebuild läuft über `npm run dev`.

Tests und Qualitätschecks:

```powershell
php artisan test
php artisan platform:plugins
npm run build
```

## Plugin-Entwicklung

Ein Plugin implementiert `App\Plugins\Plugin` und liefert seine
Identität sowie seine Dashboard-Daten:

```php
<?php

namespace Vendor\Catalog;

use App\Plugins\Plugin;

final class CatalogPlugin implements Plugin
{
    public function id(): string { return 'vendor.catalog'; }
    public function name(): string { return 'Katalog'; }
    public function version(): string { return '1.0.0'; }
    public function description(): string { return 'Produkte und Kategorien verwalten.'; }
    public function icon(): string { return '◇'; }
    public function accent(): string { return 'secondary'; }
    public function url(): string { return route('catalog.index'); }
}
```

Das Plugin wird in `config/plugins.php` registriert:

```php
'manifests' => [
    Vendor\Catalog\CatalogPlugin::class,
],
```

Ein externes Plugin sollte seine eigene Struktur mitbringen:

```text
src/
  CatalogPlugin.php
  CatalogServiceProvider.php
  Http/Controllers/
  Models/
  database/migrations/
  resources/views/
  routes/web.php
composer.json
```

Das Plugin darf eigene Migrationen, Modelle, Routen, Views, Events, Jobs und
Berechtigungen enthalten. Der Nexora-Kern sollte nur über stabile Contracts
und öffentliche Services angesprochen werden. Keine Plugin-Daten dürfen ohne
Organisation-ID gespeichert werden.

## Organisationen und Mandantentrennung

Jeder Kunde ist eine eigene `Organization`. Benutzer werden über
`organization_user` zugeordnet; die Rolle liegt auf der Zuordnung. Abos
gehören immer zu einer Organisation und verweisen über `module_id` auf ein
Modul.

Wichtige Regeln:

1. Jede fachliche Tabelle erhält eine `organization_id`.
2. Queries werden immer auf die aktive Organisation eingeschränkt.
3. Der Betreiberbereich wird mit `platform.admin` geschützt.
4. Kundenrouten benötigen `auth` und `organization.member`.
5. Niemals Organisationsdaten über eine URL-ID ungeprüft laden.

## Geschäftsmodell und Betriebsmodelle

Nexora wird in zwei Varianten angeboten:

- **Nexora Cloud:** 15 € pro Monat für Betrieb über unsere Infrastruktur,
  inklusive Plattform-Updates und Backups.
- **Self-hosted:** 50 € einmalig für die Lizenz zur Installation auf einem
  eigenen Server. Ab 100 Kunden sinkt die Lizenz auf 45 € einmalig. Betrieb,
  Backups und Updates liegen beim Kunden.

Zusätzliche Entwickler-Module können unabhängig davon einen eigenen Preis
haben. Für über Nexora vermittelte Modulumsätze fällt eine Servicegebühr von
10 % an. Entwickler können für ihr Modul einen `plan_type` verwenden:

- `one_time` für einen Festpreis
- `subscription` für ein Monatsabo

Der Preis wird in `price_cents` und die Währung in `currency` gespeichert.
Zahlungsanbieter, Rechnungen, Webhooks und Auszahlungen sind bewusst noch
nicht produktiv angebunden. Vor dem Livegang müssen diese Prozesse inklusive
Steuern, Kündigung, Rückerstattung und Anbieter-Onboarding ergänzt werden.

## E-Mail

Die zentrale Nexora-Adresse ist:

```text
nexora@austit.de
```

## Betreiberzugang

Betreiber melden sich über denselben Login unter `/login` an. Ein
Administratorkonto wird niemals mit einem festen Passwort ins Repository
geschrieben, sondern lokal über Artisan erstellt:

```powershell
php artisan platform:create-admin nexora@austit.de --name="Nexora Betreiber"
```

Das Passwort wird sicher abgefragt. Nach der Anmeldung ist die Übersicht unter
`/admin/kunden` und die Support-Lizenzverwaltung unter `/admin/lizenzen`
erreichbar. Für Laravel-CLI-Aufrufe muss die PHP-8.4-Executable verwendet
werden, wenn eine ältere PHP-Version im PATH liegt.

Für den lokalen Betrieb nutzt das Projekt standardmäßig den `log`-Mailer.
Für echte Einladungen, Passwort-Reset und Support-Antworten müssen in `.env`
SMTP-Zugangsdaten des Mailproviders eingetragen und anschließend der
Konfigurationscache geleert werden:

```powershell
php artisan config:clear
```

## Produktions-Checkliste

- `APP_ENV=production` und `APP_DEBUG=false`
- HTTPS und korrekte `APP_URL`
- SMTP konfigurieren und Absender-Domain verifizieren
- `php artisan migrate --force`
- `php artisan config:cache`
- `php artisan route:cache`
- `npm run build`
- Backups und Queue-Worker einrichten
- Zahlungsanbieter und Abo-Webhooks integrieren
- Organisationsfilter in jedem neuen Modul testen
