<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stundenplan — Nexora</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="platform-page app-shell">
            @include('platform.sidebar')
            <div class="app-content">
                <header class="module-header">
                    <span class="module-version">🏫 {{ $organization->name }}</span>
                    <span class="module-version">Stundenplan · Wochenraster</span>
                </header>

                <main class="platform-main school-main">
                    <div class="module-topbar">
                        <div>
                            <p class="eyebrow">Unterrichtsorganisation</p>
                            <h1>Stundenplan</h1>
                            <p class="module-lead">Wochenübersicht planen, Räume koordinieren und Lehrkräfte zuweisen.</p>
                        </div>
                        <button class="primary-button" onclick="document.getElementById('add-lesson-dialog').showModal()">＋ Stunde eintragen</button>
                    </div>

                    @if (session('status'))
                        <div class="form-success">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="form-errors">{{ $errors->first() }}</div>
                    @endif

                    <!-- Filter Toolbar -->
                    <div class="filter-toolbar">
                        <div class="class-pills">
                            <span class="filter-label">Klasse:</span>
                            @foreach ($classes as $cls)
                                <a href="{{ route('timetable.index', ['class' => $cls]) }}" class="class-pill {{ $selectedClass === $cls && !$teacherFilter ? 'active' : '' }}">
                                    {{ $cls }}
                                </a>
                            @endforeach
                        </div>

                        <form method="GET" action="{{ route('timetable.index') }}" class="teacher-filter-form">
                            <span class="filter-label">Lehrkraft:</span>
                            <select name="teacher_id" onchange="this.form.submit()" class="filter-select">
                                <option value="">Alle Lehrkräfte / nach Klasse</option>
                                @foreach ($teachers as $t)
                                    <option value="{{ $t->id }}" {{ $teacherFilter == $t->id ? 'selected' : '' }}>
                                        {{ $t->name }} ({{ $t->code }})
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <!-- Timetable Weekly Grid -->
                    <div class="timetable-scroll-wrapper">
                        <div class="timetable-matrix">
                            <!-- Matrix Header: Days -->
                            <div class="timetable-header-cell timetable-time-col">Zeit / Stunde</div>
                            @foreach ($days as $dayNum => $dayName)
                                <div class="timetable-header-cell">
                                    <strong>{{ $dayName }}</strong>
                                </div>
                            @endforeach

                            <!-- Matrix Rows: Periods 1 to 8 -->
                            @foreach ($periods as $pNum => $pTimes)
                                <div class="timetable-period-label timetable-time-col">
                                    <strong>{{ $pNum }}. Stunde</strong>
                                    <small>{{ $pTimes['start'] }} – {{ $pTimes['end'] }}</small>
                                </div>

                                @foreach ($days as $dayNum => $dayName)
                                    @php
                                        $entry = $grid[$pNum][$dayNum] ?? null;
                                    @endphp
                                    <div class="timetable-slot {{ $entry ? 'has-lesson' : 'empty-slot' }}">
                                        @if ($entry)
                                            <div class="timetable-card badge-{{ $entry->color }}">
                                                <div class="lesson-header">
                                                    <strong class="lesson-subject">{{ $entry->subject }}</strong>
                                                    <form method="POST" action="{{ route('timetable.destroy', $entry) }}" onsubmit="return confirm('Stunde wirklich löschen?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="lesson-delete-btn" title="Löschen">✕</button>
                                                    </form>
                                                </div>
                                                <div class="lesson-details">
                                                    @if ($entry->room)
                                                        <span class="lesson-tag">🚪 {{ $entry->room }}</span>
                                                    @endif
                                                    @if ($entry->teacher)
                                                        <span class="lesson-tag">👤 {{ $entry->teacher->code }}</span>
                                                    @endif
                                                    @if ($teacherFilter)
                                                        <span class="lesson-tag class-tag">Kl. {{ $entry->class_name }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <button type="button" class="slot-add-btn" onclick="openAddModal({{ $dayNum }}, {{ $pNum }})">
                                                ＋
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>

                    <!-- Native Modal Dialog for Adding a Lesson -->
                    <dialog id="add-lesson-dialog" class="modern-dialog">
                        <div class="dialog-content">
                            <div class="dialog-header">
                                <div>
                                    <p class="eyebrow">Stundenplan</p>
                                    <h2>Unterrichtsstunde anlegen</h2>
                                </div>
                                <button type="button" class="dialog-close-btn" onclick="document.getElementById('add-lesson-dialog').close()">✕</button>
                            </div>

                            <form method="POST" action="{{ route('timetable.store') }}" class="dialog-form">
                                @csrf
                                <div class="form-grid-2">
                                    <label>
                                        Klasse
                                        <input name="class_name" id="modal-class-input" value="{{ $selectedClass ?: '8a' }}" required placeholder="z. B. 8a">
                                    </label>
                                    <label>
                                        Fach
                                        <input name="subject" required placeholder="z. B. Mathematik, Deutsch">
                                    </label>
                                </div>

                                <div class="form-grid-2">
                                    <label>
                                        Wochentag
                                        <select name="day_of_week" id="modal-day-select" required>
                                            @foreach ($days as $num => $name)
                                                <option value="{{ $num }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                    <label>
                                        Stunde
                                        <select name="period" id="modal-period-select" required>
                                            @foreach ($periods as $num => $times)
                                                <option value="{{ $num }}">{{ $num }}. Stunde ({{ $times['start'] }} - {{ $times['end'] }})</option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>

                                <div class="form-grid-2">
                                    <label>
                                        Raum
                                        <input name="room" placeholder="z. B. R104, Physik-1">
                                    </label>
                                    <label>
                                        Lehrkraft
                                        <select name="teacher_id">
                                            <option value="">Keine / Auswählen</option>
                                            @foreach ($teachers as $t)
                                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->code }})</option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>

                                <label>
                                    Farbmarkierung
                                    <select name="color">
                                        <option value="mint">Mint (Hellgrün)</option>
                                        <option value="coral">Koralle (Rot-Orange)</option>
                                        <option value="yellow">Sonnengelb</option>
                                        <option value="sky">Himmelblau</option>
                                        <option value="purple">Lavendel / Lila</option>
                                    </select>
                                </label>

                                <div class="dialog-actions">
                                    <button type="button" class="secondary-button" onclick="document.getElementById('add-lesson-dialog').close()">Abbrechen</button>
                                    <button type="submit" class="primary-button">Stunde speichern →</button>
                                </div>
                            </form>
                        </div>
                    </dialog>
                </main>
            </div>
        </div>

        <script>
            function openAddModal(day, period) {
                const modal = document.getElementById('add-lesson-dialog');
                if (day) document.getElementById('modal-day-select').value = day;
                if (period) document.getElementById('modal-period-select').value = period;
                modal.showModal();
            }
        </script>
    </body>
</html>
