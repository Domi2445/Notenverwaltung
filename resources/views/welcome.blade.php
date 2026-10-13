<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nexora — Modulare Plattform</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="shell">
            <aside class="sidebar">
                <a class="brand" href="/">
                    <span class="brand-mark">N</span>
                    <span>
                        <strong>Nexora</strong>
                        <small>modulare Plattform</small>
                    </span>
                </a>

                <div class="workspace-switcher">
                    <span class="workspace-avatar">D</span>
                    <span class="workspace-copy">
                        <small>Arbeitsbereich</small>
                        <strong>Meine Organisation</strong>
                    </span>
                    <span class="chevron">⌄</span>
                </div>

                <nav class="navigation" aria-label="Hauptnavigation">
                    <p class="nav-label">Übersicht</p>
                    <a class="nav-item active" href="/">
                        <span class="nav-icon">⌂</span> Startseite
                    </a>
                    <a class="nav-item" href="{{ route('marketplace') }}">
                        <span class="nav-icon">◇</span> Module
                    </a>
                    <a class="nav-item" href="#aktivitaet">
                        <span class="nav-icon">↗</span> Aktivität
                    </a>
                    <p class="nav-label">Verwalten</p>
                    <a class="nav-item" href="{{ route('settings') }}">
                        <span class="nav-icon">⚙</span> Einstellungen
                    </a>
                    <a class="nav-item" href="{{ route('team') }}">
                        <span class="nav-icon">♧</span> Team & Rechte
                    </a>
                </nav>

                <div class="sidebar-footer">
                    <a class="help-card" href="{{ route('support') }}">
                        <span class="help-icon">?</span>
                        <div>
                            <strong>Fragen?</strong>
                            <small>Wir helfen dir weiter.</small>
                        </div>
                    </div>
                    <div class="user-row">
                        <span class="user-avatar">D</span>
                        <span class="user-copy">
                            <strong>Dominik</strong>
                            <small>Administrator</small>
                        </span>
                        <span class="more">•••</span>
                    </div>
                </div>
            </aside>

            <main class="main-content">
                <header class="topbar">
                    <div class="breadcrumb"><span>Arbeitsbereich</span><b>/</b><strong>Startseite</strong></div>
                    <div class="topbar-actions">
                        <button class="icon-button" aria-label="Benachrichtigungen">◌<i></i></button>
                        <button class="avatar-button">D</button>
                    </div>
                </header>

                <div class="content">
                    <section class="hero">
                        <div>
                            <p class="eyebrow">Dienstag, 13. Oktober 2026</p>
                            <h1>Guten Morgen, <em>Dominik.</em></h1>
                            <p class="hero-copy">Alles, was deine Organisation bewegt,<br>an einem Ort.</p>
                        </div>
                        <div class="hero-shape" aria-hidden="true">
                            <span class="shape-ring ring-one"></span>
                            <span class="shape-ring ring-two"></span>
                            <span class="shape-dot dot-one"></span>
                            <span class="shape-dot dot-two"></span>
                        </div>
                    </section>

                    <section class="section-heading" id="module">
                        <div>
                            <p class="eyebrow">Dein Arbeitsplatz</p>
                            <h2>Module</h2>
                        </div>
                        <a class="text-link" href="{{ route('marketplace') }}">Alle Module <span>→</span></a>
                    </section>

                    <section class="module-grid">
                        @forelse ($plugins as $plugin)
                            <a class="module-card module-{{ $plugin->accent() }}" href="{{ $plugin->url() }}">
                                <span class="card-icon">{{ $plugin->icon() }}</span>
                                <span class="card-tag">Aktiv · v{{ $plugin->version() }}</span>
                                <h3>{{ $plugin->name() }}</h3>
                                <p>{{ $plugin->description() }}</p>
                                <span class="card-arrow">→</span>
                            </a>
                        @empty
                            <div class="module-card module-empty">
                                <span class="add-icon">＋</span>
                                <h3>Noch keine Module</h3>
                                <p>Installiere ein Modul,<br>um Nexora zu erweitern.</p>
                            </div>
                        @endforelse
                        <a class="module-card module-empty" href="{{ route('marketplace') }}">
                            <span class="add-icon">＋</span>
                            <h3>Modul hinzufügen</h3>
                            <p>Erweitere Nexora<br>nach deinen Regeln.</p>
                        </a>
                    </section>

                    <section class="lower-grid">
                        <div class="panel activity-panel" id="aktivitaet">
                            <div class="panel-heading">
                                <div><p class="eyebrow">Was passiert</p><h2>Letzte Aktivität</h2></div>
                                <a class="text-link" href="#aktivitaet">Verlauf <span>→</span></a>
                            </div>
                            <div class="activity-empty">
                                <span class="empty-mark">◒</span>
                                <h3>Noch ganz ruhig hier.</h3>
                                <p>Aktivitäten aus deinen Modulen<br>werden hier angezeigt.</p>
                            </div>
                        </div>
                        <div class="panel status-panel" id="einstellungen">
                            <div class="panel-heading"><div><p class="eyebrow">System</p><h2>Alles im Lot</h2></div><span class="status-dot"></span></div>
                            <div class="status-line"><span>Datenbank</span><strong><i></i> Verbunden</strong></div>
                            <div class="status-line"><span>Plugins</span><strong>{{ $plugins->count() }} aktiv</strong></div>
                            <div class="status-line"><span>Version</span><strong class="mono">v1.0.0-alpha</strong></div>
                            <div class="status-note">Nexora wächst mit dir.</div>
                        </div>
                    </section>
                    <footer><span>Nexora</span><span>Eine Plattform für deine Prozesse.</span><span>v1.0.0-alpha</span></footer>
                </div>
            </main>
        </div>
    </body>
</html>
