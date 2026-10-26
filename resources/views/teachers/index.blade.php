@extends('layouts.app')

@section('title', 'Lehrkräfte')
@section('eyebrow', 'Kollegium')
@section('heading', 'Lehrkräfte')
@section('lead', 'Lehrkräfte organisieren, Kürzel festlegen und Deputate überblicken.')

@php $isAdmin = auth()->user()->isAdmin(); @endphp

@if ($isAdmin)
    @section('actions')
        <button class="btn btn-primary" onclick="document.getElementById('add-teacher-dialog').showModal()">
            <x-icon name="plus" size="16" /> Lehrkraft eintragen
        </button>
    @endsection
@endif

@section('content')
    <div class="toolbar">
        <span class="toolbar-label">{{ $teachers->count() }} {{ $teachers->count() === 1 ? 'Lehrkraft' : 'Lehrkräfte' }}</span>

        <form method="GET" action="{{ route('teachers.index') }}" class="search">
            <x-icon name="search" size="16" />
            <input type="search" name="search" value="{{ $search }}" placeholder="Name, Kürzel oder Fach …" aria-label="Lehrkräfte suchen">
        </form>
    </div>

    @if ($teachers->isEmpty())
        <div class="card empty">
            <x-icon name="users" size="22" />
            <p>Keine Lehrkräfte gefunden.</p>
            @if ($isAdmin)
                <small>Lege über „Lehrkraft eintragen“ das Kollegium an.</small>
            @endif
        </div>
    @else
        <div class="entity-grid">
            @foreach ($teachers as $teacher)
                <article class="card entity">
                    <div class="entity-header">
                        <span class="avatar avatar-lg avatar-code">{{ $teacher->code }}</span>
                        <div class="entity-title">
                            <h3>{{ $teacher->name }}</h3>
                            <small>Kürzel {{ $teacher->code }}</small>
                        </div>
                        @if ($isAdmin)
                            <div class="chips">
                                @if (Route::has('users.store'))
                                    <button type="button" class="btn-icon" title="Login anlegen" aria-label="Login für {{ $teacher->name }} anlegen"
                                            onclick="openAccountDialog({{ $teacher->id }}, @js($teacher->name), @js($teacher->email))">
                                        <x-icon name="user" size="16" />
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('teachers.destroy', $teacher) }}" onsubmit="return confirm('Lehrkraft wirklich löschen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-danger" title="Löschen" aria-label="Lehrkraft löschen">
                                        <x-icon name="trash" size="16" />
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <div class="entity-body">
                        @if ($teacher->subjects)
                            <div class="chips">
                                @foreach (array_map('trim', explode(',', $teacher->subjects)) as $subject)
                                    <span class="tag tag-primary">{{ $subject }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if ($teacher->email)
                            <span class="meta-row"><x-icon name="mail" size="15" /> <a href="mailto:{{ $teacher->email }}">{{ $teacher->email }}</a></span>
                        @endif
                        @if ($teacher->phone)
                            <span class="meta-row"><x-icon name="phone" size="15" /> {{ $teacher->phone }}</span>
                        @endif
                        @if ($teacher->notes)
                            <p class="entity-note">{{ $teacher->notes }}</p>
                        @endif
                    </div>

                    <div class="entity-footer">
                        <div class="chips">
                            <span class="meta-row"><x-icon name="calendar" size="15" /> {{ $teacher->timetable_entries_count }} Std./Woche</span>
                            <span class="meta-row"><x-icon name="clipboard" size="15" /> {{ $teacher->exams_count }} Arbeiten</span>
                        </div>
                        <a href="{{ route('timetable.index', ['teacher_id' => $teacher->id]) }}" class="card-link">Stundenplan <x-icon name="arrow-right" size="14" /></a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    @include('partials.account-dialog', ['role' => 'teacher'])

    @if ($isAdmin)
    <x-modal id="add-teacher-dialog" title="Lehrkraft eintragen" :action="route('teachers.store')" submit="Lehrkraft speichern">
        <label class="field">
            <span>Name (inkl. Titel)</span>
            <input name="name" required placeholder="z. B. Dr. Martin Schmidt">
        </label>
        <label class="field">
            <span>Kürzel</span>
            <input name="code" required maxlength="10" placeholder="z. B. SCH">
        </label>
        <label class="field field-wide">
            <span>Unterrichtsfächer <em>kommagetrennt</em></span>
            <input name="subjects" placeholder="z. B. Mathematik, Physik">
        </label>
        <label class="field">
            <span>E-Mail</span>
            <input type="email" name="email" placeholder="lehrkraft@schule.de">
        </label>
        <label class="field">
            <span>Telefon <em>optional</em></span>
            <input name="phone" placeholder="z. B. 0170 1234567">
        </label>
        <label class="field field-wide">
            <span>Bemerkungen / Funktionen</span>
            <textarea name="notes" rows="2" placeholder="z. B. Klassenleitung 8a"></textarea>
        </label>
    </x-modal>
    @endif
@endsection
