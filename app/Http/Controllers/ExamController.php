<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    // GET /arbeiten – Route "exams.index" (siehe docs/PLAN.md, Schritt 7)
    public function index(Request $request)
    {
        // TODO:
        // 1. Filter aus der URL: class und status ("planned" oder "completed")
        // 2. Abfrage mit Lehrkraft und Noten (die Oberfläche zeigt beides an)
        //    -> Exam::with(['teacher', 'grades'])
        //    Filter anwenden wie bei den Noten, dann nach Datum sortieren und laden.
        // 3. Die drei Kacheln oben brauchen Zahlen über ALLE Arbeiten (ohne Filter):
        //    - $plannedCount   -> Anzahl mit status "planned"
        //    - $completedCount -> Anzahl mit status "completed"
        //    - $upcomingCount  -> geplant UND Datum ab heute
        // 4. $classes: Klassennamen aus Schülern und Arbeiten, ohne Doppelte, sortiert
        // 5. $teachers: alle Lehrkräfte nach Name (für die Auswahl im Formular)
        // 6. View "exams.index" mit allen Variablen zurückgeben
    }

    // POST /arbeiten – Route "exams.store"
    public function store(Request $request)
    {
        // TODO:
        // 1. Eingaben prüfen:
        //    - title, subject, class_name, exam_date: Pflicht (exam_date als 'date')
        //    - type: Pflicht, nur die Werte aus dem Formular erlaubt
        //      -> 'in:Klassenarbeit,Kurzarbeit/Test,Klausur,Referat,Mündliche Prüfung'
        //    - weighting: Pflicht, Zahl, z. B. zwischen 0.5 und 3
        //    - teacher_id: optional, muss in teachers existieren
        //    - status: Pflicht, nur planned oder completed
        //    - topics: optional
        // 2. Speichern und mit Meldung zurück zur Liste
    }

    // POST /arbeiten/{exam}/status – Route "exams.status"
    public function updateStatus(Exam $exam)
    {
        // TODO: Status umschalten: aus "planned" wird "completed" und umgekehrt.
        // -> Neuen Wert bestimmen (z. B. mit dem ternären Operator ? :), dann $exam->update([...])
        // Danach mit Meldung zurück.
    }

    // DELETE /arbeiten/{exam} – Route "exams.destroy"
    public function destroy(Exam $exam)
    {
        // TODO: Arbeit löschen und mit Meldung zurück zur Liste.
        // Was passiert mit den Noten, die zu dieser Arbeit gehören? (Siehe deine grades-Migration.)
    }
}
