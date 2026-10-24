<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    // GET /noten – Route "grades.index" (siehe docs/PLAN.md, Schritt 6)
    public function index(Request $request)
    {
        // TODO:
        // 1. Drei Filter aus der URL lesen: class, subject, search
        //    -> $classFilter, $subjectFilter, $search
        // 2. Abfrage aufbauen: Grade::query()
        //    - Klasse gesetzt?  -> where('class_name', ...)
        //    - Fach gesetzt?    -> where('subject', ...)
        //    - Suche gesetzt?   -> where('student_name', 'like', "%...%")
        // 3. Neueste zuerst laden -> latest()->get()  → $grades
        //
        // 4. Statistik aus den geladenen Noten berechnen (Collection-Methoden, keine neue Abfrage):
        //    - $totalCount   -> $grades->count()
        //    - $averageGrade -> $grades->avg('grade'), auf 2 Stellen runden, null wenn keine Noten
        //
        // 5. Notenspiegel $distribution: wie viele Noten fallen in jede Stufe 1 bis 6?
        //    Regel: 1,0–1,5 → 1 | 1,6–2,5 → 2 | 2,6–3,5 → 3 | 3,6–4,5 → 4 | 4,6–5,5 → 5 | 5,6–6,0 → 6
        //    Vorschlag: Array mit 1 => 0 … 6 => 0 anlegen, dann mit foreach über $grades laufen
        //    und für jede Note die passende Stufe um 1 erhöhen.
        //    Kniffliger Punkt: Wie berechnest du die Stufe? Tipp: round() rundet 1,5 auf 2 –
        //    hier soll 1,5 aber noch eine 1 sein. Überlege, ob ceil($note - 0.5) hilft,
        //    und teste es mit 1,5 / 1,6 / 6,0.
        //
        // 6. Daten für die Filter-Buttons und das Formular:
        //    - $classes  -> alle Klassennamen aus Noten UND Schülern, ohne Doppelte, sortiert
        //    - $subjects -> alle Fächer aus den Noten, ohne Doppelte
        //    - $students -> alle Schüler (für die Auswahlliste), sortiert nach Klasse und Name
        //    - $exams    -> alle Arbeiten, neueste zuerst
        //
        // 7. View "grades.index" mit allen Variablen zurückgeben (Liste in docs/PLAN.md, Schritt 6)
    }

    // POST /noten – Route "grades.store"
    public function store(Request $request)
    {
        // TODO:
        // 1. Eingaben prüfen:
        //    - student_id: optional, muss in der Tabelle students existieren -> ['nullable', 'exists:students,id']
        //    - exam_id: optional, muss in exams existieren
        //    - student_name, class_name: optional (werden ggf. vom Schüler übernommen)
        //    - subject: Pflicht
        //    - grade: Pflicht, Zahl zwischen 1 und 6 -> ['required', 'numeric', 'between:1,6']
        //    - assessment, comment: optional
        // 2. Wurde ein Schüler ausgewählt (student_id gesetzt)?
        //    -> Schüler laden (Student::find) und dessen Name und Klasse übernehmen.
        //       Die Klasse nur übernehmen, wenn keine andere eingetragen wurde.
        // 3. Steht danach immer noch kein Name fest? Dann mit Fehler zurück:
        //    -> back()->withErrors(['student_name' => '...'])->withInput()
        // 4. Note speichern -> Grade::create([...])
        // 5. Zurück zur Notenseite mit Meldung "Die Note wurde gespeichert."
    }

    // DELETE /noten/{grade} – Route "grades.destroy"
    public function destroy(Grade $grade)
    {
        // TODO: Note löschen und mit Meldung zurück -> back()->with('status', '...')
        // Warum back() statt route()? Damit die gesetzten Filter (z. B. ?class=8a) erhalten bleiben.
    }
}
