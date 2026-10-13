<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Schul-Dashboard — Nexora</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="platform-page app-shell">
            @include('platform.sidebar')
            <div class="app-content">
                <header class="module-header">
                    <span class="module-version">🏫 {{ $organization->name }}</span>
                    <span class="module-version">Schulverwaltung · {{ \Carbon\Carbon::now()->locale('de')->isoFormat('dddd, D. MMMM YYYY') }}</span>
                </header>

                <main class="platform-main school-main">
                    @if (session('status'))
                        <div class="form-success">{{ session('status') }}</div>
                    @endif

                    <!-- Header Banner -->
                    <div class="dashboard-hero">
                        <div>
                            <p class="eyebrow">Schulportal &amp; Organisation</p>
                            <h1>Guten Tag, {{ auth()->user()->name }}.</h1>
                            <p class="module-lead">Zentrale Übersicht über Schüler, Lehrkräfte, Unterrichtsstunden, Prüfungen und Noten.</p>
                        </div>
                        <div class="dashboard-quick-actions">
                            <a href="{{ route('grades.index') }}" class="primary-button">＋ Note erfassen</a>
                            <a href="{{ route('exams.index') }}" class="secondary-button">＋ Arbeit planen</a>
                            <a href="{{ route('timetable.index') }}" class="secondary-button">＋ Stundenplan</a>
                        </div>
                    </div>

                    <!-- KPI Statistics Cards -->
                    <section class="school-stats-grid">
                        <div class="kpi-card">
                            <div class="kpi-icon kpi-mint">🎓</div>
                            <div>
                                <span class="kpi-label">Schüler/innen</span>
                                <strong class="kpi-value">{{ $studentCount }}</strong>
                                <small class="kpi-hint">in {{ $classes->count() }} {{ $classes->count() === 1 ? 'Klasse' : 'Klassen' }}</small>
                            </div>
                        </div>

                        <div class="kpi-card">
                            <div class="kpi-icon kpi-sky">🧑‍🏫</div>
                            <div>
                                <span class="kpi-label">Lehrkräfte</span>
                                <strong class="kpi-value">{{ $teacherCount }}</strong>
                                <small class="kpi-hint">im aktiven Kollegium</small>
                            </div>
                        </div>

                        <div class="kpi-card">
                            <div class="kpi-icon kpi-coral">📝</div>
                            <div>
                                <span class="kpi-label">Anstehende Arbeiten</span>
                                <strong class="kpi-value">{{ $upcomingExams->count() }}</strong>
                                <small class="kpi-hint">geplant in nächster Zeit</small>
                            </div>
                        </div>

                        <div class="kpi-card">
                            <div class="kpi-icon kpi-yellow">✦</div>
                            <div>
                                <span class="kpi-label">Notendurchschnitt</span>
                                <strong class="kpi-value">{{ $averageGrade ? number_format($averageGrade, 2, ',', '.') : '—' }}</strong>
                                <small class="kpi-hint">aus {{ $gradesCount }} Bewertungen</small>
                            </div>
                        </div>
                    </section>

                    <!-- Main Grid: Today's Schedule & Upcoming Exams -->
                    <div class="dashboard-columns">
                        <!-- Left Column: Timetable today & Recent Grades -->
                        <div class="dashboard-col-main">
                            <!-- Today's Schedule Card -->
                            <section class="content-panel">
                                <div class="panel-header">
                                    <div>
                                        <p class="eyebrow">Tagesplan</p>
                                        <h2>Heutiger Unterricht ({{ \App\Models\TimetableEntry::$days[$currentDay] ?? 'Montag' }})</h2>
                                    </div>
                                    <a href="{{ route('timetable.index') }}" class="panel-link">Vollständiger Wochenplan →</a>
                                </div>

                                @if ($todaySchedule->isEmpty())
                                    <div class="panel-empty">
                                        <p>Heute sind noch keine Unterrichtsstunden eingetragen.</p>
                                        <a href="{{ route('timetable.index') }}" class="quiet-link">Stundenplan bearbeiten →</a>
                                    </div>
                                @else
                                    <div class="today-schedule-list">
                                        @foreach ($todaySchedule as $className => $classEntries)
                                            <div class="schedule-class-block">
                                                <div class="schedule-class-badge">Klasse {{ $className }}</div>
                                                <div class="schedule-row-cards">
                                                    @foreach ($classEntries as $entry)
                                                        <div class="schedule-mini-card badge-{{ $entry->color }}">
                                                            <div class="card-time">{{ $entry->period }}. Std ({{ $entry->time_start }})</div>
                                                            <div class="card-subject">{{ $entry->subject }}</div>
                                                            <div class="card-meta">
                                                                @if ($entry->room)<span>🚪 {{ $entry->room }}</span>@endif
                                                                @if ($entry->teacher)<span>👤 {{ $entry->teacher->code }}</span>@endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </section>

                            <!-- Recent Grades -->
                            <section class="content-panel mt-6">
                                <div class="panel-header">
                                    <div>
                                        <p class="eyebrow">Leistungsnachweise</p>
                                        <h2>Zuletzt eingetragene Noten</h2>
                                    </div>
                                    <a href="{{ route('grades.index') }}" class="panel-link">Alle Noten einsehen →</a>
                                </div>

                                @if ($recentGrades->isEmpty())
                                    <div class="panel-empty">
                                        <p>Noch keine Noten eingetragen.</p>
                                        <a href="{{ route('grades.index') }}" class="quiet-link">Erste Note vergeben →</a>
                                    </div>
                                @else
                                    <div class="grades-feed">
                                        @foreach ($recentGrades as $grade)
                                            <div class="grade-feed-item">
                                                <div class="feed-student-info">
                                                    <span class="student-avatar">{{ strtoupper(substr($grade->student_name, 0, 1)) }}</span>
                                                    <div>
                                                        <strong>{{ $grade->student_name }}</strong>
                                                        <small>{{ $grade->class_name }} · {{ $grade->subject }} @if ($grade->assessment) · {{ $grade->assessment }} @endif</small>
                                                    </div>
                                                </div>
                                                <div class="feed-grade-badge grade-level-{{ floor($grade->grade) }}">
                                                    {{ number_format($grade->grade, 1, ',', '.') }}
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </section>
                        </div>

                        <!-- Right Column: Upcoming Exams & School Overview -->
                        <div class="dashboard-col-side">
                            <!-- Upcoming Exams -->
                            <section class="content-panel">
                                <div class="panel-header">
                                    <div>
                                        <p class="eyebrow">Prüfungskalender</p>
                                        <h2>Anstehende Arbeiten</h2>
                                    </div>
                                    <a href="{{ route('exams.index') }}" class="panel-link">Planer →</a>
                                </div>

                                @if ($upcomingExams->isEmpty())
                                    <div class="panel-empty">
                                        <p>Keine anstehenden Arbeiten eingetragen.</p>
                                        <a href="{{ route('exams.index') }}" class="quiet-link">Neue Arbeit planen →</a>
                                    </div>
                                @else
                                    <div class="upcoming-exams-list">
                                        @foreach ($upcomingExams as $exam)
                                            <div class="exam-compact-card">
                                                <div class="exam-compact-date">
                                                    <strong>{{ $exam->exam_date->format('d.') }}</strong>
                                                    <small>{{ $exam->exam_date->locale('de')->shortMonthName }}</small>
                                                </div>
                                                <div class="exam-compact-details">
                                                    <div class="exam-compact-title">{{ $exam->title }}</div>
                                                    <div class="exam-compact-meta">
                                                        <span>Klasse {{ $exam->class_name }}</span>
                                                        <span>{{ $exam->subject }}</span>
                                                        <span class="exam-badge-type">{{ $exam->type }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </section>

                            <!-- Quick Navigation Links -->
                            <section class="content-panel mt-6">
                                <p class="eyebrow">Schnellzugriff</p>
                                <h2>Bereiche der Schule</h2>
                                <div class="quick-nav-list">
                                    <a href="{{ route('students.index') }}" class="quick-nav-item">
                                        <span>🎓 Schülerkartei &amp; Klassen</span>
                                        <strong>{{ $studentCount }} erfasst →</strong>
                                    </a>
                                    <a href="{{ route('teachers.index') }}" class="quick-nav-item">
                                        <span>🧑‍🏫 Lehrkräfte &amp; Fächer</span>
                                        <strong>{{ $teacherCount }} aktiv →</strong>
                                    </a>
                                    <a href="{{ route('timetable.index') }}" class="quick-nav-item">
                                        <span>📅 Stundenplanverwaltung</span>
                                        <strong>Wochenmatrix →</strong>
                                    </a>
                                    <a href="{{ route('grades.index') }}" class="quick-nav-item">
                                        <span>✦ Noten &amp; Zeugnisnoten</span>
                                        <strong>{{ $gradesCount }} Einträge →</strong>
                                    </a>
                                </div>
                            </section>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
