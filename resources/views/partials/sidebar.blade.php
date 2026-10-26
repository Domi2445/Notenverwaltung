@php
    $user = auth()->user();

    // 'roles': wer den Menüpunkt sieht
    $navigation = [
        ['route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'dashboard', 'label' => 'Übersicht', 'roles' => ['admin', 'teacher', 'student']],
        ['route' => 'grades.index', 'match' => 'grades.*', 'icon' => 'star', 'label' => $user->isStudent() ? 'Meine Noten' : 'Noten', 'roles' => ['admin', 'teacher', 'student']],
        ['route' => 'timetable.index', 'match' => 'timetable.*', 'icon' => 'calendar', 'label' => 'Stundenplan', 'roles' => ['admin', 'teacher', 'student']],
        ['route' => 'exams.index', 'match' => 'exams.*', 'icon' => 'clipboard', 'label' => 'Arbeiten & Tests', 'roles' => ['admin', 'teacher', 'student']],
        ['route' => 'students.index', 'match' => 'students.*', 'icon' => 'graduation', 'label' => 'Schüler', 'roles' => ['admin', 'teacher']],
        ['route' => 'teachers.index', 'match' => 'teachers.*', 'icon' => 'users', 'label' => 'Lehrkräfte', 'roles' => ['admin', 'teacher']],
    ];
    $navigation = array_filter($navigation, fn ($item) => in_array($user->role, $item['roles']));

    $roleLabel = $user->roleLabel();
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
            <small>{{ $roleLabel ? $roleLabel.' · ' : '' }}{{ $user->email }}</small>
        </span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-icon" title="Abmelden" aria-label="Abmelden">
                <x-icon name="logout" />
            </button>
        </form>
    </div>
</aside>
