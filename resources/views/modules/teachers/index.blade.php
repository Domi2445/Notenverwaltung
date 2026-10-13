<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Kollegium — Nexora</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="platform-page app-shell">
            @include('platform.sidebar')
            <div class="app-content">
                <header class="module-header">
                    <span class="module-version">🏫 {{ $organization->name }}</span>
                    <span class="module-version">Lehrkräfte &amp; Kollegium</span>
                </header>

                <main class="platform-main school-main">
                    <div class="module-topbar">
                        <div>
                            <p class="eyebrow">Kollegium &amp; Fächer</p>
                            <h1>Lehrkräfte</h1>
                            <p class="module-lead">Lehrpersonal organisieren, Kürzel definieren und Unterrichtsdeputate überblicken.</p>
                        </div>
                        <button class="primary-button" onclick="document.getElementById('add-teacher-dialog').showModal()">＋ Lehrkraft eintragen</button>
                    </div>

                    @if (session('status'))
                        <div class="form-success">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="form-errors">{{ $errors->first() }}</div>
                    @endif

                    <!-- Filter / Search Toolbar -->
                    <div class="filter-toolbar">
                        <span class="filter-label">Kollegium: {{ $teachers->count() }} aktiv</span>

                        <form method="GET" action="{{ route('teachers.index') }}" class="search-form">
                            <input type="search" name="search" value="{{ $search }}" placeholder="Name, Kürzel oder Fach suchen..." class="search-input">
                            <button type="submit" class="search-btn">Suchen</button>
                        </form>
                    </div>

                    <!-- Teachers Grid -->
                    <div class="teachers-grid">
                        @forelse ($teachers as $teacher)
                            <article class="teacher-card">
                                <div class="teacher-card-header">
                                    <div class="teacher-avatar-badge">
                                        <span class="teacher-code">{{ $teacher->code }}</span>
                                    </div>
                                    <div class="teacher-title-wrap">
                                        <h3 class="teacher-name">{{ $teacher->name }}</h3>
                                        <small class="teacher-sub">Kürzel: {{ $teacher->code }}</small>
                                    </div>
                                    <form method="POST" action="{{ route('teachers.destroy', $teacher) }}" onsubmit="return confirm('Lehrkraft wirklich löschen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="lesson-delete-btn" title="Löschen">✕</button>
                                    </form>
                                </div>

                                <div class="teacher-body">
                                    @if ($teacher->subjects)
                                        <div class="teacher-subjects-list">
                                            @foreach (array_map('trim', explode(',', $teacher->subjects)) as $subj)
                                                <span class="teacher-subject-tag">{{ $subj }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="teacher-contact-info">
                                        @if ($teacher->email)
                                            <div class="contact-row">
                                                <span>✉</span>
                                                <a href="mailto:{{ $teacher->email }}">{{ $teacher->email }}</a>
                                            </div>
                                        @endif
                                        @if ($teacher->phone)
                                            <div class="contact-row">
                                                <span>📞</span>
                                                <span>{{ $teacher->phone }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    @if ($teacher->notes)
                                        <p class="teacher-notes">{{ $teacher->notes }}</p>
                                    @endif
                                </div>

                                <div class="teacher-card-footer">
                                    <div class="teacher-deputat">
                                        <span>📅 {{ $teacher->timetable_entries_count }} Wochenstunden</span>
                                        <span>📝 {{ $teacher->exams_count }} Arbeiten</span>
                                    </div>
                                    <a href="{{ route('timetable.index', ['teacher_id' => $teacher->id]) }}" class="teacher-schedule-link">Stundenplan ansehen →</a>
                                </div>
                            </article>
                        @empty
                            <div class="admin-empty">
                                Keine Lehrkräfte gefunden.
                                <small>Klicke oben auf „＋ Lehrkraft eintragen“, um das Kollegium anzulegen.</small>
                            </div>
                        @endforelse
                    </div>

                    <!-- Native Modal Dialog for Adding a Teacher -->
                    <dialog id="add-teacher-dialog" class="modern-dialog">
                        <div class="dialog-content">
                            <div class="dialog-header">
                                <div>
                                    <p class="eyebrow">Kollegium</p>
                                    <h2>Neue Lehrkraft erfassen</h2>
                                </div>
                                <button type="button" class="dialog-close-btn" onclick="document.getElementById('add-teacher-dialog').close()">✕</button>
                            </div>

                            <form method="POST" action="{{ route('teachers.store') }}" class="dialog-form">
                                @csrf
                                <div class="form-grid-2">
                                    <label>
                                        Name (inkl. Titel)
                                        <input name="name" required placeholder="z. B. Dr. Martin Schmidt">
                                    </label>
                                    <label>
                                        Lehrerkürzel
                                        <input name="code" required maxlength="10" placeholder="z. B. SCH, MEY">
                                    </label>
                                </div>

                                <label>
                                    Unterrichtsfächer (kommagetrennt)
                                    <input name="subjects" placeholder="z. B. Mathematik, Physik, Informatik">
                                </label>

                                <div class="form-grid-2">
                                    <label>
                                        E-Mail
                                        <input type="email" name="email" placeholder="lehrkraft@schule.de">
                                    </label>
                                    <label>
                                        Telefon (optional)
                                        <input name="phone" placeholder="z. B. 0170 1234567">
                                    </label>
                                </div>

                                <label>
                                    Bemerkungen / Funktionen
                                    <textarea name="notes" rows="2" placeholder="z. B. Klassenleitung 8a, Fachbereichsleiter..."></textarea>
                                </label>

                                <div class="dialog-actions">
                                    <button type="button" class="secondary-button" onclick="document.getElementById('add-teacher-dialog').close()">Abbrechen</button>
                                    <button type="submit" class="primary-button">Lehrkraft speichern →</button>
                                </div>
                            </form>
                        </div>
                    </dialog>
                </main>
            </div>
        </div>
    </body>
</html>
