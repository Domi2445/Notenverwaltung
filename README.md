# Notenverwaltung

Webanwendung für eine Schule: Noten erfassen und auswerten, Schüler und
Lehrkräfte verwalten, Klassenarbeiten planen und Stundenpläne pflegen.
Umgesetzt mit Laravel 13, Blade und Vite.

## Funktionen

- Anmeldung und Registrierung
- Schülerkartei mit Klassenfilter, Suche und Notenschnitt
- Lehrkräfte mit Kürzel, Fächern und Deputat
- Noten mit Filtern, Statistik und Notenspiegel (Verteilung 1–6)
- Arbeiten und Prüfungen mit Gewichtung und Status
- Wochenstundenplan pro Klasse oder Lehrkraft
- Übersicht mit Kennzahlen, heutigem Unterricht und anstehenden Arbeiten

## Arbeitsteilung und KI-Einsatz

| Teil | Erstellt von |
|---|---|
| Oberfläche: Blade-Views, Layout, CSS-Design-System, Icons (`resources/views`, `resources/css`) | mit KI erstellt (Claude, Anthropic) |
| Arbeitsplan mit Anforderungen pro Seite (`docs/PLAN.md`) | mit KI erstellt, Anforderungen von mir |
| Datenbank: Migrationen, Models, Beziehungen | selbst programmiert |
| Logik: Routen, Controller, Validierung, Anmeldung, Berechnungen, Seeder | selbst programmiert |

Die Commits der KI-Teile sind mit `Co-Authored-By: Claude` gekennzeichnet.

## Einrichtung

Voraussetzungen: PHP 8.3+, Composer, Node.js und npm.

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Danach ist die App unter http://127.0.0.1:8000 erreichbar.

Tests: `php artisan test`
