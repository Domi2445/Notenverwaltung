@extends('layouts.app')

@section('title', 'Übersicht')
@section('eyebrow', 'Übersicht')
@section('heading', 'Guten Tag, '.auth()->user()->name.'.')
@section('lead', 'Schüler, Lehrkräfte, Unterricht, Arbeiten und Noten auf einen Blick.')

@section('actions')
    <a href="{{ route('grades.index') }}" class="btn btn-primary">
        <x-icon name="plus" size="16" /> Note erfassen
    </a>
    <a href="{{ route('exams.index') }}" class="btn btn-secondary">Arbeit planen</a>
@endsection

@section('content')
    <section class="stats">
        <div class="stat">
            <span class="stat-icon tone-indigo"><x-icon name="graduation" /></span>
            <div>
                <span class="stat-label">Schüler/innen</span>
                <strong class="stat-value">{{ $studentCount }}</strong>
                <small class="stat-hint">in {{ $classes->count() }} {{ $classes->count() === 1 ? 'Klasse' : 'Klassen' }}</small>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon tone-sky"><x-icon name="users" /></span>
            <div>
                <span class="stat-label">Lehrkräfte</span>
                <strong class="stat-value">{{ $teacherCount }}</strong>
                <small class="stat-hint">im Kollegium</small>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon tone-amber"><x-icon name="clipboard" /></span>
            <div>
                <span class="stat-label">Anstehende Arbeiten</span>
                <strong class="stat-value">{{ $upcomingExams->count() }}</strong>
                <small class="stat-hint">in nächster Zeit</small>
            </div>
        </div>
        <div class="stat">
            <span class="stat-icon tone-emerald"><x-icon name="trending" /></span>
            <div>
                <span class="stat-label">Notendurchschnitt</span>
                <strong class="stat-value">{{ $averageGrade ? number_format($averageGrade, 2, ',', '.') : '—' }}</strong>
                <small class="stat-hint">aus {{ $gradesCount }} Bewertungen</small>
            </div>
        </div>
    </section>

    <div class="split">
        <div class="stack">
            <section class="card">
                <div class="card-header">
                    <div>
                        <h2>Heutiger Unterricht</h2>
                        <p class="card-subtitle">{{ $dayName }}</p>
                    </div>
                    <a href="{{ route('timetable.index') }}" class="card-link">Wochenplan <x-icon name="arrow-right" size="14" /></a>
                </div>

                @if ($todaySchedule->isEmpty())
                    <div class="empty">
                        <x-icon name="calendar" size="22" />
                        <p>Heute sind keine Unterrichtsstunden eingetragen.</p>
                        <a href="{{ route('timetable.index') }}" class="card-link">Stundenplan bearbeiten</a>
                    </div>
                @else
                    <div class="today">
                        @foreach ($todaySchedule as $className => $classEntries)
                            <div class="today-class">
                                <span class="tag">Klasse {{ $className }}</span>
                                <div class="today-lessons">
                                    @foreach ($classEntries as $entry)
                                        <div class="lesson tone-{{ $entry->color }}">
                                            <span class="lesson-time">{{ $entry->period }}. Std · {{ $entry->time_start }}</span>
                                            <strong>{{ $entry->subject }}</strong>
                                            <span class="lesson-meta">
                                                @if ($entry->room)<span><x-icon name="pin" size="12" /> {{ $entry->room }}</span>@endif
                                                @if ($entry->teacher)<span><x-icon name="user" size="12" /> {{ $entry->teacher->code }}</span>@endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="card">
                <div class="card-header">
                    <div>
                        <h2>Zuletzt eingetragene Noten</h2>
                        <p class="card-subtitle">Die neuesten Leistungsnachweise</p>
                    </div>
                    <a href="{{ route('grades.index') }}" class="card-link">Alle Noten <x-icon name="arrow-right" size="14" /></a>
                </div>

                @if ($recentGrades->isEmpty())
                    <div class="empty">
                        <x-icon name="star" size="22" />
                        <p>Noch keine Noten eingetragen.</p>
                        <a href="{{ route('grades.index') }}" class="card-link">Erste Note vergeben</a>
                    </div>
                @else
                    <ul class="list">
                        @foreach ($recentGrades as $grade)
                            <li class="list-item">
                                <span class="avatar">{{ mb_strtoupper(mb_substr($grade->student_name, 0, 1)) }}</span>
                                <div class="list-text">
                                    <strong>{{ $grade->student_name }}</strong>
                                    <small>{{ $grade->class_name }} · {{ $grade->subject }}@if ($grade->assessment) · {{ $grade->assessment }}@endif</small>
                                </div>
                                <x-grade :value="$grade->grade" />
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

        <div class="stack">
            <section class="card">
                <div class="card-header">
                    <div>
                        <h2>Anstehende Arbeiten</h2>
                        <p class="card-subtitle">Prüfungskalender</p>
                    </div>
                    <a href="{{ route('exams.index') }}" class="card-link">Planer <x-icon name="arrow-right" size="14" /></a>
                </div>

                @if ($upcomingExams->isEmpty())
                    <div class="empty">
                        <x-icon name="clipboard" size="22" />
                        <p>Keine anstehenden Arbeiten.</p>
                        <a href="{{ route('exams.index') }}" class="card-link">Neue Arbeit planen</a>
                    </div>
                @else
                    <ul class="list">
                        @foreach ($upcomingExams as $exam)
                            <li class="list-item">
                                <span class="date-chip">
                                    <strong>{{ $exam->exam_date->format('d') }}</strong>
                                    <small>{{ $exam->exam_date->locale('de')->shortMonthName }}</small>
                                </span>
                                <div class="list-text">
                                    <strong>{{ $exam->title }}</strong>
                                    <small>Klasse {{ $exam->class_name }} · {{ $exam->subject }} · {{ $exam->type }}</small>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="card">
                <div class="card-header">
                    <h2>Schnellzugriff</h2>
                </div>
                <nav class="quick-links">
                    <a href="{{ route('students.index') }}">
                        <x-icon name="graduation" /> <span>Schülerkartei</span> <small>{{ $studentCount }}</small>
                    </a>
                    <a href="{{ route('teachers.index') }}">
                        <x-icon name="users" /> <span>Lehrkräfte</span> <small>{{ $teacherCount }}</small>
                    </a>
                    <a href="{{ route('timetable.index') }}">
                        <x-icon name="calendar" /> <span>Stundenplan</span> <small>Woche</small>
                    </a>
                    <a href="{{ route('grades.index') }}">
                        <x-icon name="star" /> <span>Noten</span> <small>{{ $gradesCount }}</small>
                    </a>
                </nav>
            </section>
        </div>
    </div>
@endsection
