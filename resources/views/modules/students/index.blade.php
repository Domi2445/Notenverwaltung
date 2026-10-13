<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Schülerkartei — Nexora</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="platform-page app-shell">
            @include('platform.sidebar')
            <div class="app-content">
                <header class="module-header">
                    <span class="module-version">🏫 {{ $organization->name }}</span>
                    <span class="module-version">Schülerkartei &amp; Klassen</span>
                </header>

                <main class="platform-main school-main">
                    <div class="module-topbar">
                        <div>
                            <p class="eyebrow">Stammdatenverwaltung</p>
                            <h1>Schülerinnen &amp; Schüler</h1>
                            <p class="module-lead">Klassenverbände verwalten, persönliche Leistungen und Notenschnitte einsehen.</p>
                        </div>
                        <button class="primary-button" onclick="document.getElementById('add-student-dialog').showModal()">＋ Schüler/in aufnehmen</button>
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
                            <a href="{{ route('students.index', ['search' => $search]) }}" class="class-pill {{ !$classFilter ? 'active' : '' }}">Alle</a>
                            @foreach ($classes as $c)
                                <a href="{{ route('students.index', ['class' => $c, 'search' => $search]) }}" class="class-pill {{ $classFilter === $c ? 'active' : '' }}">
                                    Klasse {{ $c }}
                                </a>
                            @endforeach
                        </div>

                        <form method="GET" action="{{ route('students.index') }}" class="search-form">
                            @if ($classFilter)<input type="hidden" name="class" value="{{ $classFilter }}">@endif
                            <input type="search" name="search" value="{{ $search }}" placeholder="Name oder E-Mail suchen..." class="search-input">
                            <button type="submit" class="search-btn">Suchen</button>
                        </form>
                    </div>

                    <!-- Students Grid / List -->
                    <div class="students-grid">
                        @forelse ($students as $student)
                            @php
                                $avg = $student->averageGrade();
                            @endphp
                            <article class="student-card">
                                <div class="student-card-header">
                                    <div class="student-meta-left">
                                        <span class="student-avatar-lg">{{ strtoupper(substr($student->name, 0, 1)) }}</span>
                                        <div>
                                            <h3 class="student-name">{{ $student->name }}</h3>
                                            <span class="student-class-badge">Klasse {{ $student->class_name }}</span>
                                        </div>
                                    </div>

                                    @if ($avg !== null)
                                        <div class="student-avg-badge grade-level-{{ floor($avg) }}" title="Notenschnitt">
                                            <small>Schnitt</small>
                                            <strong>{{ number_format($avg, 2, ',', '.') }}</strong>
                                        </div>
                                    @else
                                        <div class="student-avg-badge grade-level-none">
                                            <small>Schnitt</small>
                                            <strong>—</strong>
                                        </div>
                                    @endif
                                </div>

                                <div class="student-details-body">
                                    @if ($student->email)
                                        <div class="student-info-row">
                                            <span>✉</span>
                                            <a href="mailto:{{ $student->email }}">{{ $student->email }}</a>
                                        </div>
                                    @endif
                                    @if ($student->birthday)
                                        <div class="student-info-row">
                                            <span>🎂</span>
                                            <span>{{ $student->birthday->format('d.m.Y') }}</span>
                                        </div>
                                    @endif
                                    @if ($student->notes)
                                        <p class="student-notes">{{ $student->notes }}</p>
                                    @endif
                                </div>

                                <div class="student-card-footer">
                                    <div class="student-grades-preview">
                                        <small>{{ $student->grades->count() }} {{ $student->grades->count() === 1 ? 'Note' : 'Noten' }}:</small>
                                        <div class="mini-grades-row">
                                            @foreach ($student->grades->take(4) as $g)
                                                <span class="mini-grade-chip grade-level-{{ floor($g->grade) }}" title="{{ $g->subject }}: {{ $g->grade }}">
                                                    {{ number_format($g->grade, 1, ',', '.') }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Schüler/in wirklich löschen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="lesson-delete-btn" title="Löschen">✕</button>
                                    </form>
                                </div>
                            </article>
                        @empty
                            <div class="admin-empty">
                                Keine Schüler/innen gefunden.
                                <small>Klicke oben auf „＋ Schüler/in aufnehmen“, um die Liste zu befüllen.</small>
                            </div>
                        @endforelse
                    </div>

                    <!-- Native Modal Dialog for Adding a Student -->
                    <dialog id="add-student-dialog" class="modern-dialog">
                        <div class="dialog-content">
                            <div class="dialog-header">
                                <div>
                                    <p class="eyebrow">Schülerkartei</p>
                                    <h2>Neue/n Schüler/in aufnehmen</h2>
                                </div>
                                <button type="button" class="dialog-close-btn" onclick="document.getElementById('add-student-dialog').close()">✕</button>
                            </div>

                            <form method="POST" action="{{ route('students.store') }}" class="dialog-form">
                                @csrf
                                <div class="form-grid-2">
                                    <label>
                                        Vollständiger Name
                                        <input name="name" required placeholder="z. B. Sophie Keller">
                                    </label>
                                    <label>
                                        Klasse
                                        <input name="class_name" value="{{ $classFilter ?: '8a' }}" required placeholder="z. B. 8a, 9b, 10a">
                                    </label>
                                </div>

                                <div class="form-grid-2">
                                    <label>
                                        E-Mail (optional)
                                        <input type="email" name="email" placeholder="schueler@schule.de">
                                    </label>
                                    <label>
                                        Geburtsdatum (optional)
                                        <input type="date" name="birthday">
                                    </label>
                                </div>

                                <label>
                                    Notizen / Förderbedarf
                                    <textarea name="notes" rows="2" placeholder="Besonderheiten, Bemerkungen..."></textarea>
                                </label>

                                <div class="dialog-actions">
                                    <button type="button" class="secondary-button" onclick="document.getElementById('add-student-dialog').close()">Abbrechen</button>
                                    <button type="submit" class="primary-button">Schüler/in anlegen →</button>
                                </div>
                            </form>
                        </div>
                    </dialog>
                </main>
            </div>
        </div>
    </body>
</html>
