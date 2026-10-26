<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\TimetableEntry;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    // GET /stundenplan – Route "timetable.index" (siehe docs/PLAN.md, Schritt 8)
    public function index(Request $request)
    {
        // TODO:
        // 1. $classes: alle Klassennamen aus Stundenplan und Schülern, ohne Doppelte, sortiert
        // 2. Auswahl aus der URL:
        //    - $teacherFilter = ?teacher_id   (zeigt den Plan einer Lehrkraft)
        //    - $selectedClass = ?class, sonst die erste Klasse aus $classes
        // 3. Einträge laden (mit Lehrkraft):
        //    - Wenn $teacherFilter gesetzt: alle Stunden dieser Lehrkraft
        //    - sonst: alle Stunden der gewählten Klasse
        // 4. Das Raster $grid bauen, das die Oberfläche als $grid[stunde][tag] liest:
        //    -> leeres Array anlegen, dann foreach über die Einträge:
        //       $grid[$entry->period][$entry->day_of_week] = $entry;
        //    Leere Felder müssen nicht gesetzt werden, die Oberfläche prüft mit "?? null".
        // 5. $days    -> TimetableEntry::DAYS
        //    $periods -> TimetableEntry::PERIODS
        //    $teachers -> alle Lehrkräfte nach Name
        // 6. View "timetable.index" mit allen Variablen zurückgeben

        $user = $request->user();

        if ($user->isStudent()) {
            // Schüler sehen nur den Plan ihrer eigenen Klasse
            $classes = collect([$user->studentClass()])->filter();
            $teacherFilter = null;
            $selectedClass = $user->studentClass();
        } else {
            $classes = TimetableEntry::pluck('class_name')->merge(Student::pluck('class_name'))->unique()->sort()->values();
            $teacherFilter = $request->query('teacher_id');
            $selectedClass = $request->query('class', $classes->first());
        }

        $query = TimetableEntry::with('teacher');

        if ($teacherFilter) {
            $query->where('teacher_id', $teacherFilter);
        } else {
            $query->where('class_name', $selectedClass);
        }

        $grid = [];
        foreach ($query->get() as $entry) {
            $grid[$entry->period][$entry->day_of_week] = $entry;
        }

        $days = TimetableEntry::DAYS;
        $periods = TimetableEntry::PERIODS;
        $teachers = Teacher::orderBy('name')->get();

        return view('timetable.index', compact('classes', 'teacherFilter', 'selectedClass', 'grid', 'days', 'periods', 'teachers'));
    }

    // POST /stundenplan – Route "timetable.store"
    public function store(Request $request)
    {
        // TODO:
        // 1. Eingaben prüfen:
        //    - class_name, subject: Pflicht
        //    - day_of_week: Pflicht, Zahl von 1 bis 5 -> 'between:1,5'
        //    - period: Pflicht, Zahl von 1 bis 9 (Berufsschule: 9 Stunden)
        //    - teacher_id: optional, muss existieren
        //    - room: optional
        //    - color: optional, nur mint, sky, purple, coral, yellow
        // 2. Start- und Endzeit aus TimetableEntry::PERIODS zur gewählten Stunde holen
        //    und als time_start / time_end mitspeichern.
        // 3. Ist die Stunde für diese Klasse schon belegt? Zwei Möglichkeiten:
        //    a) mit Fehlermeldung abweisen, oder
        //    b) den alten Eintrag ersetzen -> TimetableEntry::updateOrCreate([Klasse, Tag, Stunde], [Rest])
        //    Entscheide dich und begründe es kurz im Handbuch.
        // 4. Zurück zum Stundenplan DIESER Klasse mit Meldung
        //    -> redirect()->route('timetable.index', ['class' => ...])

        $validated = $request->validate(
            [
                'class_name' => 'required',
                'subject' => 'required',
                'day_of_week' => 'required|integer|between:1,5',
                'period' => 'required|integer|between:1,9',
                'teacher_id' => 'nullable|exists:teachers,id',
                'room' => 'nullable',
                'color' => 'nullable|in:mint,sky,purple,coral,yellow',
            ]
        );

        $times = TimetableEntry::PERIODS[$validated['period']];
        $validated['time_start'] = $times['start'];
        $validated['time_end'] = $times['end'];
        $validated['color'] = $validated['color'] ?? 'mint';

        // Ist die Stunde schon belegt, wird der alte Eintrag ersetzt
        TimetableEntry::updateOrCreate(
            [
                'class_name' => $validated['class_name'],
                'day_of_week' => $validated['day_of_week'],
                'period' => $validated['period'],
            ],
            $validated
        );

        return redirect()->route('timetable.index', ['class' => $validated['class_name']])->with('status', 'Stunde erfolgreich gespeichert.');
    }

    // DELETE /stundenplan/{entry} – Route "timetable.destroy"
    public function destroy(TimetableEntry $entry)
    {
        // TODO: Eintrag löschen und mit Meldung zurück zum Stundenplan der Klasse.
        $entry->delete();

        return redirect()->route('timetable.index', ['class' => $entry->class_name])->with('status', 'Eintrag erfolgreich gelöscht.');
    }
}
