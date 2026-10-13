<aside class="app-sidebar">
    <a class="sidebar-brand" href="{{ auth()->user()->is_platform_admin ? route('admin.customers') : route('customer.dashboard') }}">
        <span class="brand-mark">N</span>
        <div class="brand-text">
            <strong>Nexora</strong>
            <small>Schulportal</small>
        </div>
    </a>

    <p class="sidebar-label">{{ auth()->user()->is_platform_admin ? 'Betreiber' : 'Schulverwaltung' }}</p>
    <nav class="sidebar-nav">
        @if (auth()->user()->is_platform_admin)
            <a class="{{ request()->routeIs('admin.customers') ? 'active' : '' }}" href="{{ route('admin.customers') }}">
                <span class="sidebar-icon">▦</span>
                <span class="sidebar-title">Kunden &amp; Abos</span>
            </a>
            <a class="{{ request()->routeIs('admin.licenses') ? 'active' : '' }}" href="{{ route('admin.licenses') }}">
                <span class="sidebar-icon">◇</span>
                <span class="sidebar-title">Lizenzen</span>
            </a>
        @else
            <a class="{{ request()->routeIs('customer.dashboard') ? 'active' : '' }}" href="{{ route('customer.dashboard') }}">
                <span class="sidebar-icon">⌂</span>
                <span class="sidebar-title">Übersicht</span>
            </a>
            <a class="{{ request()->routeIs('grades.*') ? 'active' : '' }}" href="{{ route('grades.index') }}">
                <span class="sidebar-icon">✦</span>
                <span class="sidebar-title">Notenverwaltung</span>
            </a>
            <a class="{{ request()->routeIs('timetable.*') ? 'active' : '' }}" href="{{ route('timetable.index') }}">
                <span class="sidebar-icon">📅</span>
                <span class="sidebar-title">Stundenplan</span>
            </a>
            <a class="{{ request()->routeIs('exams.*') ? 'active' : '' }}" href="{{ route('exams.index') }}">
                <span class="sidebar-icon">📝</span>
                <span class="sidebar-title">Arbeiten &amp; Tests</span>
            </a>
            <a class="{{ request()->routeIs('students.*') ? 'active' : '' }}" href="{{ route('students.index') }}">
                <span class="sidebar-icon">🎓</span>
                <span class="sidebar-title">Schüler</span>
            </a>
            <a class="{{ request()->routeIs('teachers.*') ? 'active' : '' }}" href="{{ route('teachers.index') }}">
                <span class="sidebar-icon">🧑‍🏫</span>
                <span class="sidebar-title">Lehrkräfte</span>
            </a>

            <div class="sidebar-divider"></div>
            <p class="sidebar-label">Plattform</p>

            <a class="{{ request()->routeIs('marketplace') ? 'active' : '' }}" href="{{ route('marketplace') }}">
                <span class="sidebar-icon">◇</span>
                <span class="sidebar-title">Module</span>
            </a>
            <a class="{{ request()->routeIs('team') ? 'active' : '' }}" href="{{ route('team') }}">
                <span class="sidebar-icon">♧</span>
                <span class="sidebar-title">Team &amp; Rechte</span>
            </a>
            <a class="{{ request()->routeIs('settings') ? 'active' : '' }}" href="{{ route('settings') }}">
                <span class="sidebar-icon">⚙</span>
                <span class="sidebar-title">Einstellungen</span>
            </a>
        @endif
    </nav>

    <div class="sidebar-bottom">
        <a href="{{ route('support') }}">
            <span class="sidebar-icon">?</span>
            <span class="sidebar-title">Hilfe &amp; Support</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">
                <span class="sidebar-icon">↪</span>
                <span class="sidebar-title">Abmelden</span>
            </button>
        </form>
    </div>
</aside>
