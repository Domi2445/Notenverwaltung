<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Notenverwaltung — Nexora</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="platform-page app-shell">
            @include('platform.sidebar')
            <div class="app-content">
                <header class="module-header">
                    <span class="module-version">🏫 {{ $organization->name }}</span>
                    <span class="module-version">Notenverwaltung · Leistungsübersicht</span>
                </header>

                <main class="platform-main school-main">
                    <div class="grades-heading">
                        <div>
                            <p class="eyebrow">Nexora Modul</p>
                            <h1>Notenverwaltung</h1>
                            <p class="module-lead">Leistungen übersichtlich erfassen und sicher im Blick behalten.</p>
                        </div>
                        <span class="module-status"><i></i>Aktiver Arbeitsbereich</span>
                    </div>

                    @if (session('status'))
                        <div class="form-success">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="form-errors">{{ $errors->first() }}</div>
                    @endif

                    <!-- Stats Bar -->
                    <section class="grade-stats">
                        <div>
                            <span>Erfasste Noten</span>
                            <strong>{{ $totalCount }}</strong>
                            <small>in diesem Arbeitsbereich</small>
                        </div>
                        <div>
                            <span>Klassen</span>
                            <strong>{{ $classes->count() }}</strong>
                            <small>mit Leistungen</small>
                        </div>
                        <div>
                            <span>Fächer</span>
                            <strong>{{ $subjects->count() }}</strong>
                            <small>im aktuellen Bestand</small>
                        </div>
                        <div>
                            <span>Notenschnitt</span>
                            <strong>{{ $averageGrade ? number_format($averageGrade, 2, ',', '.') : '—' }}</strong>
                            <small>über alle Einträge</small>
                        </div>
                    </section>

                    <!-- Notenverteilung Visualisierung (Notenspiegel) -->
                    @if ($totalCount > 0)
                        <section class="content-panel mb-6">
                            <div class="panel-header">
                                <div>
                                    <p class="eyebrow">Notenspiegel</p>
                                    <h2>Verteilung der Notenstufen</h2>
                                </div>
                                <span class="panel-hint">{{ $totalCount }} bewertete Arbeiten</span>
                            </div>

                            <div class="grade-distribution-bars">
                                @for ($n = 1; $n <= 6; $n++)
                                    @php
                                        $count = $distribution[$n] ?? 0;
                                        $pct = $totalCount > 0 ? round(($count / $totalCount) * 100) : 0;
                                    @endphp
                                    <div class="distribution-col">
                                        <div class="bar-track">
                                            <div class="bar-fill grade-level-{{ $n }}" style="height: {{ max($pct, 6) }}%">
                                                @if ($count > 0)<span class="bar-count">{{ $count }}</span>@endif
                                            </div>
                                        </div>
                                        <strong class="bar-grade-label">Note {{ $n }}</strong>
                                        <small class="bar-pct">{{ $pct }}%</small>
                                    </div>
                                @endfor
                            </div>
                        </section>
                    @endif

                    <!-- Main Grid: Form Left, List Right -->
                    <section class="grades-layout">
                        <!-- Left: Form to record a grade -->
                        <form class="grade-form" method="POST" action="{{ route('grades.store') }}" id="grade-record-form">
                            @csrf
                            <div class="form-heading">
                                <span class="form-icon">＋</span>
                                <div>
                                    <p class="eyebrow">Neue Leistung</p>
                                    <h2>Note erfassen</h2>
                                </div>
                            </div>

                            @if ($students->isNotEmpty())
                                <div class="grade-fields">
                                    <label class="field-wide">
                                        Schüler/in auswählen (oder unten Freitext eingeben)
                                        <select name="student_id" id="student_select_box" onchange="syncStudentData(this)">
                                            <option value="">-- Freitext / Schüler auswählen --</option>
                                            @foreach ($students as $s)
                                                <option value="{{ $s->id }}" data-name="{{ $s->name }}" data-class="{{ $s->class_name }}" {{ old('student_id') == $s->id ? 'selected' : '' }}>
                                                    {{ $s->name }} (Klasse {{ $s->class_name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>
                            @endif

                            <div class="grade-fields">
                                <label>
                                    Schüler/in
                                    <input name="student_name" id="student_name_input" value="{{ old('student_name') }}" placeholder="Name eingeben" required>
                                </label>
                                <label>
                                    Klasse
                                    <input name="class_name" id="class_name_input" value="{{ old('class_name') }}" placeholder="z. B. 8a" required>
                                </label>
                                <label>
                                    Fach
                                    <input name="subject" value="{{ old('subject') }}" placeholder="z. B. Mathematik" required>
                                </label>
                                <label>
                                    Note
                                    <input type="number" name="grade" value="{{ old('grade') }}" min="1" max="6" step="0.1" placeholder="1,0 – 6,0" required>
                                </label>

                                @if ($exams->isNotEmpty())
                                    <label class="field-wide">
                                        Zugehörige Arbeit / Prüfung
                                        <select name="exam_id" onchange="syncExamSubject(this)">
                                            <option value="">Keine Arbeit zuordnen (Sonstige Leistung)</option>
                                            @foreach ($exams as $e)
                                                <option value="{{ $e->id }}" data-subject="{{ $e->subject }}" data-class="{{ $e->class_name }}" data-title="{{ $e->title }}" {{ old('exam_id') == $e->id ? 'selected' : '' }}>
                                                    {{ $e->title }} ({{ $e->subject }}, Kl. {{ $e->class_name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                @endif

                                <label class="field-wide">
                                    Leistung / Prüfung
                                    <input name="assessment" id="assessment_input" value="{{ old('assessment') }}" placeholder="z. B. Klassenarbeit 1, Mündliche Mitarbeit">
                                </label>
                                <label class="field-wide">
                                    Kommentar
                                    <textarea name="comment" placeholder="Optionaler Hinweis zur Leistung">{{ old('comment') }}</textarea>
                                </label>
                            </div>

                            <button class="primary-button" type="submit">Note speichern <span>→</span></button>
                        </form>

                        <!-- Right: List of grades -->
                        <section class="grades-list">
                            <div class="list-heading">
                                <div>
                                    <p class="eyebrow">Verlauf</p>
                                    <h2>Letzte Einträge</h2>
                                </div>
                                <span class="list-count">{{ $grades->count() }} {{ $grades->count() === 1 ? 'Note' : 'Noten' }}</span>
                            </div>

                            <!-- Filter Toolbar -->
                            <div class="filter-toolbar mb-4">
                                <div class="class-pills">
                                    <a href="{{ route('grades.index', ['subject' => $subjectFilter, 'search' => $search]) }}" class="class-pill {{ !$classFilter ? 'active' : '' }}">Alle</a>
                                    @foreach ($classes as $c)
                                        <a href="{{ route('grades.index', ['class' => $c, 'subject' => $subjectFilter, 'search' => $search]) }}" class="class-pill {{ $classFilter === $c ? 'active' : '' }}">
                                            Kl. {{ $c }}
                                        </a>
                                    @endforeach
                                </div>

                                <form method="GET" action="{{ route('grades.index') }}" class="search-form">
                                    @if ($classFilter)<input type="hidden" name="class" value="{{ $classFilter }}">@endif
                                    <input type="search" name="search" value="{{ $search }}" placeholder="Schüler suchen..." class="search-input">
                                    <button type="submit" class="search-btn">Suchen</button>
                                </form>
                            </div>

                            <div class="grades-items-wrap">
                                @forelse ($grades as $entry)
                                    <article class="grade-row">
                                        <div class="grade-student">
                                            <span class="student-avatar">{{ strtoupper(substr($entry->student_name, 0, 1)) }}</span>
                                            <div>
                                                <strong>{{ $entry->student_name }}</strong>
                                                <small>{{ $entry->class_name }} · {{ $entry->subject }}@if ($entry->assessment) · {{ $entry->assessment }}@endif</small>
                                                @if ($entry->comment)
                                                    <p class="grade-comment-hint">{{ $entry->comment }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="grade-row-right">
                                            <b class="grade-value grade-level-{{ floor($entry->grade) }}">{{ number_format($entry->grade, 1, ',', '.') }}</b>
                                            <form method="POST" action="{{ route('grades.destroy', $entry) }}" onsubmit="return confirm('Note wirklich löschen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="lesson-delete-btn" title="Löschen">✕</button>
                                            </form>
                                        </div>
                                    </article>
                                @empty
                                    <div class="admin-empty">
                                        Noch keine Noten erfasst.
                                        <small>Dein erster Eintrag erscheint hier.</small>
                                    </div>
                                @endforelse
                            </div>
                        </section>
                    </section>
                </main>
            </div>
        </div>

        <script>
            function syncStudentData(select) {
                const opt = select.options[select.selectedIndex];
                if (opt && opt.dataset.name) {
                    document.getElementById('student_name_input').value = opt.dataset.name;
                    document.getElementById('class_name_input').value = opt.dataset.class;
                }
            }

            function syncExamSubject(select) {
                const opt = select.options[select.selectedIndex];
                if (opt && opt.dataset.title) {
                    document.getElementById('assessment_input').value = opt.dataset.title;
                    if (opt.dataset.class) {
                        document.getElementById('class_name_input').value = opt.dataset.class;
                    }
                }
            }
        </script>
    </body>
</html>
