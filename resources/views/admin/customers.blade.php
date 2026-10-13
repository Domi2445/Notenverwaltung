<!DOCTYPE html>
<html lang="de">
    <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Kunden — Nexora</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
    <body>
        <div class="platform-page app-shell">
            @include('platform.sidebar')
            <div class="app-content"><header class="module-header"><span class="module-version">Betreiberbereich · vertraulich</span><span class="module-version">Support-Konsole</span></header>
            <main class="platform-main">
                <p><a class="back-link" href="{{ route('admin.licenses') }}">Support-Lizenzverwaltung →</a></p>
                <p class="eyebrow">Nur für dich</p><h1>Kunden & Abos</h1>
                <p class="module-lead">Organisationen, Nutzer und aktive Modul-Abos in einer Übersicht. Kundendaten bleiben voneinander getrennt.</p>
                <section class="admin-summary"><div><strong>{{ $organizations->count() }}</strong><span>Organisationen</span></div><div><strong>{{ $organizations->sum('users_count') }}</strong><span>Benutzer</span></div><div><strong>{{ $organizations->sum(fn ($organization) => $organization->subscriptions->count()) }}</strong><span>Aktive Abos</span></div></section>
                <section class="customer-list">
                    @forelse ($organizations as $organization)
                        <article class="customer-row">
                            <div class="customer-identity"><span class="customer-avatar">{{ strtoupper(substr($organization->name, 0, 1)) }}</span><div><h2>{{ $organization->name }}</h2><small>{{ $organization->users_count }} Benutzer · {{ ucfirst($organization->status) }}</small></div></div>
                            <div class="subscription-list">@forelse ($organization->subscriptions as $subscription)<span><b>{{ $subscription->module_id }}</b><small>{{ $subscription->plan_type === 'subscription' ? 'Monatlich' : 'Festpreis' }} · {{ number_format($subscription->price_cents / 100, 2, ',', '.') }} €</small></span>@empty<span class="no-subscription">Kein kostenpflichtiges Modul</span>@endforelse</div>
                        </article>
                    @empty
                        <div class="admin-empty">Noch keine Kundenorganisation angelegt.</div>
                    @endforelse
                </section>
            </main></div>
        </div>
    </body>
</html>
