@extends('layouts.app')

@section('title', 'Schüler')
@section('eyebrow', 'Stammdaten')
@section('heading', 'Schülerinnen & Schüler')
@section('lead', 'Klassen verwalten, Leistungen und Notenschnitte einsehen.')

@php $isAdmin = auth()->user()->isAdmin(); @endphp

@if ($isAdmin)
    @section('actions')
        <button class="btn btn-primary" onclick="document.getElementById('add-student-dialog').showModal()">
            <x-icon name="plus" size="16" /> Schüler/in aufnehmen
        </button>
    @endsection
@endif

@section('content')
    <div class="toolbar">
        <div class="pills">
            <a href="{{ route('students.index', ['search' => $search]) }}" @class(['pill', 'active' => ! $classFilter])>Alle</a>
            @foreach ($classes as $c)
                <a href="{{ route('students.index', ['class' => $c, 'search' => $search]) }}" @class(['pill', 'active' => $classFilter === $c])>Klasse {{ $c }}</a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('students.index') }}" class="search">
            @if ($classFilter)<input type="hidden" name="class" value="{{ $classFilter }}">@endif
            <x-icon name="search" size="16" />
            <input type="search" name="search" value="{{ $search }}" placeholder="Name oder E-Mail suchen …" aria-label="Schüler suchen">
        </form>
    </div>

    @if ($students->isEmpty())
        <div class="card empty">
            <x-icon name="graduation" size="22" />
            <p>Keine Schüler/innen gefunden.</p>
            @if ($isAdmin)
                <small>Lege über „Schüler/in aufnehmen“ die ersten Einträge an.</small>
            @endif
        </div>
    @else
        <div class="entity-grid">
            @foreach ($students as $student)
                @php $avg = $student->averageGrade(); @endphp
                <article class="card entity">
                    <div class="entity-header">
                        <span class="avatar avatar-lg">{{ mb_strtoupper(mb_substr($student->name, 0, 1)) }}</span>
                        <div class="entity-title">
                            <h3>{{ $student->name }}</h3>
                            <span class="tag">Klasse {{ $student->class_name }}</span>
                        </div>
                        <div class="entity-score" title="Notenschnitt">
                            <small>Schnitt</small>
                            <x-grade :value="$avg" :decimals="2" />
                        </div>
                    </div>

                    <div class="entity-body">
                        @if ($student->email)
                            <span class="meta-row"><x-icon name="mail" size="15" /> <a href="mailto:{{ $student->email }}">{{ $student->email }}</a></span>
                        @endif
                        @if ($student->birthday)
                            <span class="meta-row"><x-icon name="cake" size="15" /> {{ $student->birthday->format('d.m.Y') }}</span>
                        @endif
                        @if ($student->notes)
                            <p class="entity-note">{{ $student->notes }}</p>
                        @endif
                    </div>

                    <div class="entity-footer">
                        <div class="chips">
                            <small>{{ $student->grades->count() }} {{ $student->grades->count() === 1 ? 'Note' : 'Noten' }}</small>
                            @foreach ($student->grades->take(4) as $g)
                                <x-grade :value="$g->grade" class="grade-sm" title="{{ $g->subject }}" />
                            @endforeach
                        </div>
                        @if ($isAdmin)
                            <div class="chips">
                                @if (Route::has('users.store'))
                                    <button type="button" class="btn-icon" title="Login anlegen" aria-label="Login für {{ $student->name }} anlegen"
                                            onclick="openAccountDialog({{ $student->id }}, @js($student->name), @js($student->email))">
                                        <x-icon name="user" size="16" />
                                    </button>
                                @endif
                                <form method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('Schüler/in wirklich löschen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon btn-danger" title="Löschen" aria-label="Schüler/in löschen">
                                        <x-icon name="trash" size="16" />
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    @include('partials.account-dialog', ['role' => 'student'])

    @if ($isAdmin)
    <x-modal id="add-student-dialog" title="Schüler/in aufnehmen" :action="route('students.store')" submit="Schüler/in anlegen">
        <label class="field">
            <span>Vollständiger Name</span>
            <input name="name" required placeholder="z. B. Sophie Keller">
        </label>
        <label class="field">
            <span>Klasse</span>
            <input name="class_name" value="{{ $classFilter ?: '8a' }}" required placeholder="z. B. 8a">
        </label>
        <label class="field">
            <span>E-Mail <em>optional</em></span>
            <input type="email" name="email" placeholder="schueler@schule.de">
        </label>
        <label class="field">
            <span>Geburtsdatum <em>optional</em></span>
            <input type="date" name="birthday">
        </label>
        <label class="field field-wide">
            <span>Notizen / Förderbedarf</span>
            <textarea name="notes" rows="2" placeholder="Besonderheiten, Bemerkungen …"></textarea>
        </label>
    </x-modal>
    @endif
@endsection
