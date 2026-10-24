<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // GET / – Route "dashboard" (siehe docs/PLAN.md, Schritt 9)
    public function index(Request $request)
    {
        // TODO: Alle Zahlen und Listen für die Übersicht zusammenstellen
        //
        // 1. Zähler: $studentCount, $teacherCount, $gradesCount -> Model::count()
        // 2. $averageGrade: Durchschnitt aller Noten -> Grade::avg('grade')
        //    Auf 2 Stellen runden, aber nur wenn es überhaupt Noten gibt (sonst null lassen).
        // 3. $classes: alle Klassennamen der Schüler, jeder nur einmal
        //    -> Student::pluck('class_name')->unique()
        // 4. $upcomingExams: die nächsten 5 Arbeiten ab heute, nach Datum sortiert
        //    -> where('exam_date', '>=', today()) … orderBy(...) … take(5) … get()
        // 5. $recentGrades: die 6 zuletzt eingetragenen Noten -> latest()->take(6)->get()
        // 6. Heutiger Wochentag als Zahl -> now()->dayOfWeekIso  (1 = Montag … 7 = Sonntag)
        //    Am Wochenende (6 oder 7) stattdessen Montag (1) anzeigen.
        // 7. $dayName: Name des Tages aus TimetableEntry::DAYS
        // 8. $todaySchedule: alle Stunden dieses Tages mit Lehrkraft, nach Stunde sortiert,
        //    gruppiert nach Klasse -> with('teacher') … orderBy('period') … get()->groupBy('class_name')
        //
        // Zum Schluss alles an die View "dashboard" übergeben -> view('dashboard', compact(...))
    }
}
