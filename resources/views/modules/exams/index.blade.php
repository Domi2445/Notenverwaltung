<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Arbeiten &amp; Prüfungen — Nexora</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="platform-page app-shell">
            @include('platform.sidebar')
            <div class="app-content">
                <header class="module-header">
                    <span class="module-version">🏫 {{ $organization->name }}</span>
                    <span class="module-version">Prüfungsplaner · Leistungsfeststellung</span>
                </header>

                <main class="platform-main school-main">
                    <div class="module-topbar">
                        <div>
                            <p class="eyebrow">Prüfungsmanagement</p>
                            <h1>Arbeiten &amp; Prüfungen</h1>
                            <p class="module-lead">Klassenarbeiten, Klausuren und Leistungskontrollen planen und nachverfolgen.</p>
                        </div>
                        <button class="primary-button" onclick="document.getElementById('add-exam-dialog').showModal()">＋ Neue Arbeit ansetzen</button>
                    </div>

                    @if (session('status'))
                        <div class="form-success">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="form-errors">{{ $errors->first() }}</div>
                    @endif

                    <!-- KPI Stats -->
                    <section class="school-stats-grid mb-6">
                        <div class="kpi-card">
                            <div class="kpi-icon kpi-coral">⏳</div>
                            <div>
                                <span class="kpi-label">Anstehend</span>
                                <strong class="kpi-value">{{ $upcomingCount }}</strong>
                                <small class="kpi-hint">in den kommenden Tagen</small>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-icon kpi-yellow">📅</div>
                            <div>
                                <span class="kpi-label">Geplant gesamt</span>
                                <strong class="kpi-value">{{ $plannedCount }}</strong>
                                <small class="kpi-hint">noch nicht geschrieben</small>
                            </div>
                        </div>
                        <div class="kpi-card">
                            <div class="kpi-icon kpi-mint">✓</div>
                            <div>
                                <span class="kpi-label">Abgeschlossen</span>
                                <strong class="kpi-value">{{ $completedCount }}</strong>
                                <small class="kpi-hint">geschrieben &amp; bewertet</small>
                            </div>
                        </div>
                    </section>

                    <!-- Filter Toolbar -->
                    <div class="filter-toolbar">
                        <div class="class-pills">
                            <span class="filter-label">Status:</span>
                            <a href="{{ route('exams.index', ['class' => $classFilter]) }}" class="class-pill {{ !$statusFilter ? 'active' : '' }}">Alle</a>
                            <a href="{{ route('exams.index', ['status' => 'planned', 'class' => $classFilter]) }}" class="class-pill {{ $statusFilter === 'planned' ? 'active' : '' }}">Geplant</a>
                            <a href="{{ route('exams.index', ['status' => 'completed', 'class' => $classFilter]) }}" class="class-pill {{ $statusFilter === 'completed' ? 'active' : '' }}">Abgeschlossen</a>
                        </div>

                        <form method="GET" action="{{ route('exams.index') }}" class="teacher-filter-form">
                            @if ($statusFilter)<input type="hidden" name="status" value="{{ $statusFilter }}">@endif
                            <span class="filter-label">Klasse:</span>
                            <select name="class" onchange="this.form.submit()" class="filter-select">
                                <option value="">Alle Klassen</option>
                                @foreach ($classes as $c)
                                    <option value="{{ $c }}" {{ $classFilter === $c ? 'selected' : '' }}>Klasse {{ $c }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <!-- Exam List -->
                    <section class="exam-list">
                        @forelse ($exams as $exam)
                            <article class="exam-card {{ $exam->status === 'completed' ? 'exam-completed' : '' }}">
                                <div class="exam-date-badge">
                                    <span class="exam-day">{{ $exam->exam_date->format('d') }}</span>
                                    <span class="exam-month">{{ $exam->exam_date->locale('de')->shortMonthName }}</span>
                                    <span class="exam-year">{{ $exam->exam_date->format('Y') }}</span>
                                </div>

                                <div class="exam-body">
                                    <div class="exam-header-line">
                                        <div class="exam-tags">
                                            <span class="tag-class">Klasse {{ $exam->class_name }}</span>
                                            <span class="tag-subject">{{ $exam->subject }}</span>
                                            <span class="tag-type">{{ $exam->type }}</span>
                                            <span class="tag-weight">Gewichtung: {{ $exam->weighting }}x</span>
                                        </div>
                                        <div class="exam-actions">
                                            <form method="POST" action="{{ route('exams.status', $exam) }}">
                                                @csrf
                                                <button type="submit" class="toggle-status-btn {{ $exam->status === 'completed' ? 'btn-green' : 'btn-orange' }}">
                                                    {{ $exam->status === 'completed' ? '✓ Geschrieben' : '○ Offen' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('exams.destroy', $exam) }}" onsubmit="return confirm('Arbeit wirklich löschen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="lesson-delete-btn" title="Löschen">✕</button>
                                            </form>
                                        </div>
                                    </div>

                                    <h3 class="exam-title">{{ $exam->title }}</h3>

                                    @if ($exam->topics)
                                        <p class="exam-topics"><strong>Themen:</strong> {{ $exam->topics }}</p>
                                    @endif

                                    <div class="exam-footer">
                                        @if ($exam->teacher)
                                            <span class="exam-teacher">🧑‍🏫 {{ $exam->teacher->name }} ({{ $exam->teacher->code }})</span>
                                        @endif
                                        @if ($exam->grades->isNotEmpty())
                                            <span class="exam-grades-count">✦ {{ $exam->grades->count() }} Noten eingetragen (Schnitt: {{ number_format($exam->averageGrade(), 2, ',', '.') }})</span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="admin-empty">
                                Keine Arbeiten gefunden.
                                <small>Klicke oben auf „＋ Neue Arbeit ansetzen“, um eine Klassenarbeit einzutragen.</small>
                            </div>
                        @endforelse
                    </section>

                    <!-- Native Modal Dialog for Adding an Exam -->
                    <dialog id="add-exam-dialog" class="modern-dialog">
                        <div class="dialog-content">
                            <div class="dialog-header">
                                <div>
                                    <p class="eyebrow">Prüfungsplaner</p>
                                    <h2>Neue Arbeit / Prüfung ansetzen</h2>
                                </div>
                                <button type="button" class="dialog-close-btn" onclick="document.getElementById('add-exam-dialog').close()">✕</button>
                            </div>

                            <form method="POST" action="{{ route('exams.store') }}" class="dialog-form">
                                @csrf
                                <label>
                                    Titel / Thema
                                    <input name="title" required placeholder="z. B. 2. Klassenarbeit: Lineare Gleichungssysteme">
                                </label>

                                <div class="form-grid-2">
                                    <label>
                                        Fach
                                        <input name="subject" required placeholder="z. B. Mathematik, Deutsch, Englisch">
                                    </label>
                                    <label>
                                        Klasse
                                        <input name="class_name" required placeholder="z. B. 8a, 9b, 10a">
                                    </label>
                                </div>

                                <div class="form-grid-2">
                                    <label>
                                        Datum
                                        <input type="date" name="exam_date" value="{{ date('Y-m-d') }}" required>
                                    </label>
                                    <label>
                                        Prüfungsart
                                        <select name="type">
                                            <option value="Klassenarbeit">Klassenarbeit</option>
                                            <option value="Kurzarbeit/Test">Kurzarbeit / Test</option>
                                            <option value="Klausur">Klausur</option>
                                            <option value="Referat">Referat</option>
                                            <option value="Mündliche Prüfung">Mündliche Prüfung</option>
                                        </select>
                                    </label>
                                </div>

                                <div class="form-grid-2">
                                    <label>
                                        Gewichtung
                                        <select name="weighting">
                                            <option value="1.0">1.0 (Einfache Wertung)</option>
                                            <option value="2.0" selected>2.0 (Doppelte Wertung)</option>
                                            <option value="0.5">0.5 (Halbe Wertung)</option>
                                            <option value="3.0">3.0 (Dreifache Wertung)</option>
                                        </select>
                                    </label>
                                    <label>
                                        Lehrkraft
                                        <select name="teacher_id">
                                            <option value="">Auswählen (optional)</option>
                                            @foreach ($teachers as $t)
                                                <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->code }})</option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>

                                <label>
                                    Status
                                    <select name="status">
                                        <option value="planned" selected>Geplant (steht noch an)</option>
                                        <option value="completed">Bereits geschrieben</option>
                                    </select>
                                </label>

                                <label>
                                    Themenschwerpunkte &amp; Hilfsmittel
                                    <textarea name="topics" rows="2" placeholder="z. B. Kapitel 4 &amp; 5, Taschenrechner erlaubt"></textarea>
                                </label>

                                <div class="dialog-actions">
                                    <button type="button" class="secondary-button" onclick="document.getElementById('add-exam-dialog').close()">Abbrechen</button>
                                    <button type="submit" class="primary-button">Arbeit eintragen →</button>
                                </div>
                            </form>
                        </div>
                    </dialog>
                </main>
            </div>
        </div>
    </body>
</html>
