@php
    $navigation = [
        ['route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Übersicht'],
        ['route' => 'grades.index', 'match' => 'grades.*', 'icon' => 'star', 'label' => 'Noten'],
        ['route' => 'timetable.index', 'match' => 'timetable.*', 'icon' => 'calendar', 'label' => 'Stundenplan'],
        ['route' => 'exams.index', 'match' => 'exams.*', 'icon' => 'clipboard', 'label' => 'Arbeiten & Tests'],
        ['route' => 'students.index', 'match' => 'students.*', 'icon' => 'graduation', 'label' => 'Schüler'],
        ['route' => 'teachers.index', 'match' => 'teachers.*', 'icon' => 'users', 'label' => 'Lehrkräfte'],
    ];
    $user = auth()->user();
    $initials = collect(explode(' ', $user->name))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('');
@endphp
<aside class="nv-sidebar">
    <a class="nv-brand" href="{{ route('dashboard') }}">
        <span class="nv-brand-mark">N</span>
        <span class="nv-brand-text">
            <strong>Notenverwaltung</strong>
            <small>Schulportal</small>
        </span>
    </a>

    <nav class="nv-nav" aria-label="Hauptnavigation">
        @foreach ($navigation as $item)
            <a href="{{ route($item['route']) }}" @class(['active' => request()->routeIs($item['match'])]) @if (request()->routeIs($item['match'])) aria-current="page" @endif>
                <x-icon :name="$item['icon']" />
                <span>{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <div class="nv-user">
        <span class="avatar">{{ $initials }}</span>
        <span class="nv-user-text">
            <strong>{{ $user->name }}</strong>
            <small>{{ $user->email }}</small>
        </span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-icon" title="Abmelden" aria-label="Abmelden">
                <x-icon name="logout" />
            </button>
        </form>
    </div>
</aside>
