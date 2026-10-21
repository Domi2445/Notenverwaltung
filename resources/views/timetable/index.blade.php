@extends('layouts.app')

@section('title', 'Stundenplan')
@section('eyebrow', 'Unterricht')
@section('heading', 'Stundenplan')
@section('lead', 'Wochenübersicht planen, Räume koordinieren und Lehrkräfte zuweisen.')

@section('actions')
    <button class="btn btn-primary" onclick="openAddModal()">
        <x-icon name="plus" size="16" /> Stunde eintragen
    </button>
@endsection

@section('content')
    <div class="toolbar">
        <div class="pills">
            @foreach ($classes as $cls)
                <a href="{{ route('timetable.index', ['class' => $cls]) }}" @class(['pill', 'active' => $selectedClass === $cls && ! $teacherFilter])>Klasse {{ $cls }}</a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('timetable.index') }}">
            <select name="teacher_id" onchange="this.form.submit()" class="select" aria-label="Nach Lehrkraft filtern">
                <option value="">Nach Klasse anzeigen</option>
                @foreach ($teachers as $t)
                    <option value="{{ $t->id }}" @selected($teacherFilter == $t->id)>{{ $t->name }} ({{ $t->code }})</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="card timetable-card">
        <div class="timetable-scroll">
            <div class="timetable">
                <div class="tt-head tt-corner">Stunde</div>
                @foreach ($days as $dayName)
                    <div class="tt-head">{{ $dayName }}</div>
                @endforeach

                @foreach ($periods as $pNum => $pTimes)
                    <div class="tt-time">
                        <strong>{{ $pNum }}.</strong>
                        <small>{{ $pTimes['start'] }}–{{ $pTimes['end'] }}</small>
                    </div>

                    @foreach ($days as $dayNum => $dayName)
                        @php $entry = $grid[$pNum][$dayNum] ?? null; @endphp
                        <div class="tt-slot">
                            @if ($entry)
                                <div class="lesson tone-{{ $entry->color }}">
                                    <div class="lesson-top">
                                        <strong>{{ $entry->subject }}</strong>
                                        <form method="POST" action="{{ route('timetable.destroy', $entry) }}" onsubmit="return confirm('Stunde wirklich löschen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="lesson-delete" title="Löschen" aria-label="Stunde löschen">
                                                <x-icon name="x" size="13" />
                                            </button>
                                        </form>
                                    </div>
                                    <span class="lesson-meta">
                                        @if ($entry->room)<span><x-icon name="pin" size="12" /> {{ $entry->room }}</span>@endif
                                        @if ($entry->teacher)<span><x-icon name="user" size="12" /> {{ $entry->teacher->code }}</span>@endif
                                        @if ($teacherFilter)<span>Kl. {{ $entry->class_name }}</span>@endif
                                    </span>
                                </div>
                            @else
                                <button type="button" class="tt-add" onclick="openAddModal({{ $dayNum }}, {{ $pNum }})" aria-label="Stunde am {{ $dayName }}, {{ $pNum }}. Stunde eintragen">
                                    <x-icon name="plus" size="16" />
                                </button>
                            @endif
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>

    <x-modal id="add-lesson-dialog" title="Unterrichtsstunde anlegen" :action="route('timetable.store')" submit="Stunde speichern">
        <label class="field">
            <span>Klasse</span>
            <input name="class_name" value="{{ $selectedClass ?: '8a' }}" required placeholder="z. B. 8a">
        </label>
        <label class="field">
            <span>Fach</span>
            <input name="subject" required placeholder="z. B. Mathematik">
        </label>
        <label class="field">
            <span>Wochentag</span>
            <select name="day_of_week" id="modal-day-select" required>
                @foreach ($days as $num => $name)
                    <option value="{{ $num }}">{{ $name }}</option>
                @endforeach
            </select>
        </label>
        <label class="field">
            <span>Stunde</span>
            <select name="period" id="modal-period-select" required>
                @foreach ($periods as $num => $times)
                    <option value="{{ $num }}">{{ $num }}. Stunde ({{ $times['start'] }}–{{ $times['end'] }})</option>
                @endforeach
            </select>
        </label>
        <label class="field">
            <span>Raum</span>
            <input name="room" placeholder="z. B. R104">
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
        <fieldset class="field field-wide">
            <span>Farbe</span>
            <div class="swatches">
                @foreach (['mint' => 'Mint', 'sky' => 'Blau', 'purple' => 'Lila', 'coral' => 'Koralle', 'yellow' => 'Gelb'] as $value => $label)
                    <label class="swatch tone-{{ $value }}" title="{{ $label }}">
                        <input type="radio" name="color" value="{{ $value }}" @checked($loop->first)>
                        <span class="sr-only">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>
    </x-modal>
@endsection

@push('scripts')
    <script>
        function openAddModal(day, period) {
            if (day) document.getElementById('modal-day-select').value = day;
            if (period) document.getElementById('modal-period-select').value = period;
            document.getElementById('add-lesson-dialog').showModal();
        }
    </script>
@endpush
