<?php

namespace App\Http\Controllers;

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
    }

    // POST /stundenplan – Route "timetable.store"
    public function store(Request $request)
    {
        // TODO:
        // 1. Eingaben prüfen:
        //    - class_name, subject: Pflicht
        //    - day_of_week: Pflicht, Zahl von 1 bis 5 -> 'between:1,5'
        //    - period: Pflicht, Zahl von 1 bis 8
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
    }

    // DELETE /stundenplan/{entry} – Route "timetable.destroy"
    public function destroy(TimetableEntry $entry)
    {
        // TODO: Eintrag löschen und mit Meldung zurück zum Stundenplan der Klasse.
    }
}
