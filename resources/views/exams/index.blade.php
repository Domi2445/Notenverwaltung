@extends('layouts.app')

@section('title', 'Arbeiten & Prüfungen')
@section('eyebrow', 'Prüfungsplanung')
@section('heading', 'Arbeiten & Prüfungen')
@section('lead', 'Klassenarbeiten, Klausuren und Tests planen und nachverfolgen.')

@php $canEdit = auth()->user()->canEditGrades(); @endphp

@if ($canEdit)
    @section('actions')
        <button class="btn btn-primary" onclick="document.getElementById('add-exam-dialog').showModal()">
            <x-icon name="plus" size="16" /> Arbeit ansetzen
        </button>
    @endsection
@endif

@section('content')
    <section class="stats stats-3">
        <div class="stat">
            <span class="stat-icon tone-amber"><x-icon name="clock" /></span>
            <div>
                <span class="stat-label">Anstehend</span>
                <strong class="stat-value">{{ $upcomingCount }}</strong>
                <small class="stat-hint">in den kommenden Tagen</small>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon tone-indigo"><x-icon name="calendar" /></span>
            <div>
                <span class="stat-label">Geplant</span>
                <strong class="stat-value">{{ $plannedCount }}</strong>
                <small class="stat-hint">noch nicht geschrieben</small>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon tone-emerald"><x-icon name="check-circle" /></span>
            <div>
                <span class="stat-label">Abgeschlossen</span>
                <strong class="stat-value">{{ $completedCount }}</strong>
                <small class="stat-hint">geschrieben &amp; bewertet</small>
            </div>
        </div>
    </section>

    <div class="toolbar">
        <div class="pills">
            <a href="{{ route('exams.index', ['class' => $classFilter]) }}" @class(['pill', 'active' => ! $statusFilter])>Alle</a>
            <a href="{{ route('exams.index', ['status' => 'planned', 'class' => $classFilter]) }}" @class(['pill', 'active' => $statusFilter === 'planned'])>Geplant</a>
            <a href="{{ route('exams.index', ['status' => 'completed', 'class' => $classFilter]) }}" @class(['pill', 'active' => $statusFilter === 'completed'])>Abgeschlossen</a>
        </div>

        <form method="GET" action="{{ route('exams.index') }}">
            @if ($statusFilter)<input type="hidden" name="status" value="{{ $statusFilter }}">@endif
            <select name="class" onchange="this.form.submit()" class="select" aria-label="Klasse filtern">
                <option value="">Alle Klassen</option>
                @foreach ($classes as $c)
                    <option value="{{ $c }}" @selected($classFilter === $c)>Klasse {{ $c }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if ($exams->isEmpty())
        <div class="card empty">
            <x-icon name="clipboard" size="22" />
            <p>Keine Arbeiten gefunden.</p>
            @if ($canEdit)
                <small>Über „Arbeit ansetzen“ trägst du eine Klassenarbeit ein.</small>
            @endif
        </div>
    @else
        <div class="stack">
            @foreach ($exams as $exam)
                <article @class(['card', 'exam', 'exam-done' => $exam->status === 'completed'])>
                    <div class="date-chip date-chip-lg">
                        <strong>{{ $exam->exam_date->format('d') }}</strong>
                        <small>{{ $exam->exam_date->locale('de')->shortMonthName }} {{ $exam->exam_date->format('Y') }}</small>
                    </div>

                    <div class="exam-body">
                        <div class="chips">
                            <span class="tag tag-primary">Klasse {{ $exam->class_name }}</span>
                            <span class="tag">{{ $exam->subject }}</span>
                            <span class="tag">{{ $exam->type }}</span>
                            <span class="tag">{{ rtrim(rtrim(number_format($exam->weighting, 1, ',', ''), '0'), ',') }}× gewichtet</span>
                        </div>
                        <h3>{{ $exam->title }}</h3>
                        @if ($exam->topics)
                            <p class="exam-topics">{{ $exam->topics }}</p>
                        @endif
                        <div class="chips exam-meta">
                            @if ($exam->teacher)
                                <span class="meta-row"><x-icon name="user" size="15" /> {{ $exam->teacher->name }} ({{ $exam->teacher->code }})</span>
                            @endif
                            @if ($exam->grades->isNotEmpty())
                                <span class="meta-row"><x-icon name="star" size="15" /> {{ $exam->grades->count() }} Noten · Schnitt {{ number_format($exam->averageGrade(), 2, ',', '.') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="exam-actions">
                        @if (! $canEdit)
                            <span @class(['status-toggle', 'is-done' => $exam->status === 'completed'])>
                                @if ($exam->status === 'completed')
                                    <x-icon name="check" size="14" /> Geschrieben
                                @else
                                    <x-icon name="circle" size="14" /> Offen
                                @endif
                            </span>
                        @else
                        <form method="POST" action="{{ route('exams.status', $exam) }}">
                            @csrf
                            <button type="submit" @class(['status-toggle', 'is-done' => $exam->status === 'completed']) title="Status umschalten">
                                @if ($exam->status === 'completed')
                                    <x-icon name="check" size="14" /> Geschrieben
                                @else
                                    <x-icon name="circle" size="14" /> Offen
                                @endif
                            </button>
                        </form>
                        <form method="POST" action="{{ route('exams.destroy', $exam) }}" onsubmit="return confirm('Arbeit wirklich löschen?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-icon btn-danger" title="Löschen" aria-label="Arbeit löschen">
                                <x-icon name="trash" size="16" />
                            </button>
                        </form>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    @if ($canEdit)
    <x-modal id="add-exam-dialog" title="Arbeit ansetzen" :action="route('exams.store')" submit="Arbeit eintragen">
        <label class="field field-wide">
            <span>Titel / Thema</span>
            <input name="title" required placeholder="z. B. 2. Klassenarbeit: Lineare Gleichungssysteme">
        </label>
        <label class="field">
            <span>Fach</span>
            <input name="subject" required placeholder="z. B. Mathematik">
        </label>
        <label class="field">
            <span>Klasse</span>
            <input name="class_name" required placeholder="z. B. 8a">
        </label>
        <label class="field">
            <span>Datum</span>
            <input type="date" name="exam_date" value="{{ date('Y-m-d') }}" required>
        </label>
        <label class="field">
            <span>Prüfungsart</span>
            <select name="type">
                <option value="Klassenarbeit">Klassenarbeit</option>
                <option value="Kurzarbeit/Test">Kurzarbeit / Test</option>
                <option value="Klausur">Klausur</option>
                <option value="Referat">Referat</option>
                <option value="Mündliche Prüfung">Mündliche Prüfung</option>
            </select>
        </label>
        <label class="field">
            <span>Gewichtung</span>
            <select name="weighting">
                <option value="0.5">0,5 – halbe Wertung</option>
                <option value="1.0">1,0 – einfache Wertung</option>
                <option value="2.0" selected>2,0 – doppelte Wertung</option>
                <option value="3.0">3,0 – dreifache Wertung</option>
            </select>
        </label>
        <label class="field">
            <span>Lehrkraft</span>
            <select name="teacher_id">
                <option value="">Keine Angabe</option>
                @foreach ($teachers as $t)
                    <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->code }})</option>
                @endforeach
            </select>
        </label>
        <label class="field field-wide">
            <span>Status</span>
            <select name="status">
                <option value="planned" selected>Geplant (steht noch an)</option>
                <option value="completed">Bereits geschrieben</option>
            </select>
        </label>
        <label class="field field-wide">
            <span>Themen &amp; Hilfsmittel</span>
            <textarea name="topics" rows="2" placeholder="z. B. Kapitel 4 und 5, Taschenrechner erlaubt"></textarea>
        </label>
    </x-modal>
    @endif
@endsection
