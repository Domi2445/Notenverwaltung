<!DOCTYPE html>
<html lang="de">
    <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Team & Rechte — Nexora</title>@vite(['resources/css/app.css', 'resources/js/app.js'])</head>
    <body>
        <div class="platform-page">
            <header class="module-header"><a class="back-link" href="/">← Nexora</a><span class="module-version">Verwalten · Team & Rechte</span></header>
            <main class="platform-main">
                <p class="eyebrow">Zusammenarbeit</p><h1>Team & Rechte</h1>
                <p class="module-lead">Lade Personen ein und entscheide, welche Bereiche sie in Nexora verwalten dürfen.</p>
                @if (session('status')) <div class="form-success">{{ session('status') }}</div> @endif
                @if ($errors->any()) <div class="form-errors">@foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div> @endif
                <section class="team-card">
                    <div class="team-member"><span class="user-avatar">D</span><div><strong>Dominik</strong><small>Administrator · Vollzugriff</small></div><span class="team-status">Aktiv</span></div>
                    @foreach ($members as $member)
                        <div class="team-member"><span class="user-avatar member-avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</span><div><strong>{{ $member->name }}</strong><small>{{ ucfirst($member->role) }} · {{ $member->email }}</small></div><span class="team-status team-status-pending">{{ ucfirst($member->status) }}</span></div>
                    @endforeach
                    <div class="invite-section">
                        <p class="eyebrow">Person einladen</p>
                        <form method="POST" action="{{ route('team.invite') }}" class="invite-form">
                            @csrf
                            <label>Name<input name="name" value="{{ old('name') }}" placeholder="z. B. Anna Müller" required></label>
                            <label>E-Mail<input type="email" name="email" value="{{ old('email') }}" placeholder="anna@beispiel.de" required></label>
                            <label>Rolle<select name="role" required><option value="editor">Bearbeiten</option><option value="viewer">Lesen</option><option value="administrator">Administrator</option></select></label>
                            <button type="submit">Einladung vormerken <span>→</span></button>
                        </form>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
