@extends('layouts.app')

@php
    $isStudent = auth()->user()->isStudent();
    $canEdit = auth()->user()->canEditGrades();
@endphp

@section('title', $isStudent ? 'Meine Noten' : 'Noten')
@section('eyebrow', 'Leistungen')
@section('heading', $isStudent ? 'Meine Noten' : 'Noten')
@section('lead', $isStudent ? 'Alle deine Leistungen und dein Notenschnitt auf einen Blick.' : 'Leistungen übersichtlich erfassen und sicher im Blick behalten.')

@section('content')
    <section class="stats">
        <div class="stat">
            <span class="stat-icon tone-indigo"><x-icon name="star" /></span>
            <div>
                <span class="stat-label">Erfasste Noten</span>
                <strong class="stat-value">{{ $totalCount }}</strong>
                <small class="stat-hint">insgesamt</small>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon tone-sky"><x-icon name="layers" /></span>
            <div>
                <span class="stat-label">Klassen</span>
                <strong class="stat-value">{{ $classes->count() }}</strong>
                <small class="stat-hint">mit Leistungen</small>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon tone-amber"><x-icon name="book" /></span>
            <div>
                <span class="stat-label">Fächer</span>
                <strong class="stat-value">{{ $subjects->count() }}</strong>
                <small class="stat-hint">im Bestand</small>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon tone-emerald"><x-icon name="trending" /></span>
            <div>
                <span class="stat-label">Notenschnitt</span>
                <strong class="stat-value">{{ $averageGrade ? number_format($averageGrade, 2, ',', '.') : '—' }}</strong>
                <small class="stat-hint">über alle Einträge</small>
            </div>
        </div>
    </section>

    @if ($totalCount > 0)
        <section class="card">
            <div class="card-header">
                <div>
                    <h2>Notenspiegel</h2>
                    <p class="card-subtitle">Verteilung der Notenstufen</p>
                </div>
                <span class="tag">{{ $totalCount }} Bewertungen</span>
            </div>

            <div class="distribution">
                @for ($n = 1; $n <= 6; $n++)
                    @php
                        $count = $distribution[$n] ?? 0;
                        $pct = $totalCount > 0 ? round(($count / $totalCount) * 100) : 0;
                    @endphp
                    <div class="distribution-col">
                        <span class="distribution-count">{{ $count }}</span>
                        <div class="distribution-track">
                            <div class="distribution-bar g{{ $n }}" style="height: {{ $count > 0 ? max($pct, 4) : 0 }}%"></div>
                        </div>
                        <strong>{{ $n }}</strong>
                        <small>{{ $pct }} %</small>
                    </div>
                @endfor
            </div>
        </section>
    @endif

    <div @class(['split split-form' => $canEdit])>
        @if ($canEdit)
        <form class="card" method="POST" action="{{ route('grades.store') }}">
            @csrf
            <div class="card-header">
                <div>
                    <h2>Note erfassen</h2>
                    <p class="card-subtitle">Neue Leistung eintragen</p>
                </div>
            </div>

            <div class="form-grid">
                @if ($students->isNotEmpty())
                    <label class="field field-wide">
                        <span>Schüler/in auswählen</span>
                        <select name="student_id" id="student_select_box" onchange="syncStudentData(this)">
                            <option value="">Freitext verwenden</option>
                            @foreach ($students as $s)
                                <option value="{{ $s->id }}" data-name="{{ $s->name }}" data-class="{{ $s->class_name }}" @selected(old('student_id') == $s->id)>
                                    {{ $s->name }} (Klasse {{ $s->class_name }})
                                </option>
                            @endforeach
                        </select>
                    </label>
                @endif

                <label class="field">
                    <span>Schüler/in</span>
                    <input name="student_name" id="student_name_input" value="{{ old('student_name') }}" placeholder="Name eingeben" required>
                </label>
                <label class="field">
                    <span>Klasse</span>
                    <input name="class_name" id="class_name_input" value="{{ old('class_name') }}" placeholder="z. B. 8a" required>
                </label>
                <label class="field">
                    <span>Fach</span>
                    <input name="subject" value="{{ old('subject') }}" placeholder="z. B. Mathematik" required>
                </label>
                <label class="field">
                    <span>Note</span>
                    <input type="number" name="grade" value="{{ old('grade') }}" min="1" max="6" step="0.1" placeholder="1,0 – 6,0" required>
                </label>

                @if ($exams->isNotEmpty())
                    <label class="field field-wide">
                        <span>Zugehörige Arbeit</span>
                        <select name="exam_id" onchange="syncExamSubject(this)">
                            <option value="">Keine Arbeit zuordnen</option>
                            @foreach ($exams as $e)
                                <option value="{{ $e->id }}" data-subject="{{ $e->subject }}" data-class="{{ $e->class_name }}" data-title="{{ $e->title }}" @selected(old('exam_id') == $e->id)>
                                    {{ $e->title }} ({{ $e->subject }}, Kl. {{ $e->class_name }})
                                </option>
                            @endforeach
                        </select>
                    </label>
                @endif

                <label class="field field-wide">
                    <span>Leistung</span>
                    <input name="assessment" id="assessment_input" value="{{ old('assessment') }}" placeholder="z. B. Klassenarbeit 1, Mündliche Mitarbeit">
                </label>
                <label class="field field-wide">
                    <span>Kommentar</span>
                    <textarea name="comment" rows="3" placeholder="Optionaler Hinweis zur Leistung">{{ old('comment') }}</textarea>
                </label>
            </div>

            <button class="btn btn-primary btn-block" type="submit">Note speichern</button>
        </form>
        @endif

        <section class="card">
            <div class="card-header">
                <div>
                    <h2>{{ $isStudent ? 'Deine Noten' : 'Letzte Einträge' }}</h2>
                    <p class="card-subtitle">{{ $grades->count() }} {{ $grades->count() === 1 ? 'Note' : 'Noten' }}</p>
                </div>
            </div>

            @if ($canEdit)
            <div class="toolbar">
                <div class="pills">
                    <a href="{{ route('grades.index', ['subject' => $subjectFilter, 'search' => $search]) }}" @class(['pill', 'active' => ! $classFilter])>Alle</a>
                    @foreach ($classes as $c)
                        <a href="{{ route('grades.index', ['class' => $c, 'subject' => $subjectFilter, 'search' => $search]) }}" @class(['pill', 'active' => $classFilter === $c])>{{ $c }}</a>
                    @endforeach
                </div>

                <form method="GET" action="{{ route('grades.index') }}" class="search">
                    @if ($classFilter)<input type="hidden" name="class" value="{{ $classFilter }}">@endif
                    <x-icon name="search" size="16" />
                    <input type="search" name="search" value="{{ $search }}" placeholder="Schüler suchen …" aria-label="Schüler suchen">
                </form>
            </div>
            @endif

            @if ($grades->isEmpty())
                <div class="empty">
                    <x-icon name="star" size="22" />
                    <p>Noch keine Noten erfasst.</p>
                    <small>{{ $isStudent ? 'Sobald eine Lehrkraft eine Note einträgt, erscheint sie hier.' : 'Dein erster Eintrag erscheint hier.' }}</small>
                </div>
            @else
                <ul class="list">
                    @foreach ($grades as $entry)
                        <li class="list-item">
                            <span class="avatar">{{ mb_strtoupper(mb_substr($entry->student_name, 0, 1)) }}</span>
                            <div class="list-text">
                                <strong>{{ $entry->student_name }}</strong>
                                <small>{{ $entry->class_name }} · {{ $entry->subject }}@if ($entry->assessment) · {{ $entry->assessment }}@endif</small>
                                @if ($entry->comment)
                                    <p class="list-note">{{ $entry->comment }}</p>
                                @endif
                            </div>
                            <x-grade :value="$entry->grade" />
                            @if ($canEdit)
                                <form method="POST" action="{{ route('grades.destroy', $entry) }}" onsubmit="return confirm('Note wirklich löschen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-danger" title="Löschen" aria-label="Note löschen">
                                        <x-icon name="trash" size="16" />
                                    </button>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
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
@endpush
