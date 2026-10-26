<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TimetableEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Cache;


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

        $user = $request->user();

        // Schüler sehen nur eigene Noten sowie Arbeiten und Unterricht ihrer Klasse,
        // Lehrkräfte Noten und Arbeiten ihrer Klassen und ihre eigenen Stunden
        $grades = Grade::query();
        $exams = Exam::query();
        $lessons = TimetableEntry::query();
        $visibleClasses = $user->visibleClasses();
        if ($user->isStudent()) {
            $grades->where('student_id', $user->student_id ?? 0);
            $exams->whereIn('class_name', $visibleClasses);
            $lessons->whereIn('class_name', $visibleClasses);
        } elseif ($user->isTeacher()) {
            $grades->whereIn('class_name', $visibleClasses);
            $exams->whereIn('class_name', $visibleClasses);
            $lessons->where('teacher_id', $user->teacher_id ?? 0);
        }

        $studentCount = Student::count();
        $teacherCount = Teacher::count();
        $gradesCount = (clone $grades)->count();
        $averageGrade = (clone $grades)->avg('grade');
        $averageGrade = $averageGrade ? round($averageGrade, 2) : null;

        $classes = Student::pluck('class_name')->unique();
        $upcomingExams = $exams->where('exam_date', '>=', today())->orderBy('exam_date')->take(5)->get();
        $recentGrades = $grades->latest()->take(6)->get();
        $dayOfWeek = now()->dayOfWeekIso;
        $dayOfWeek = $dayOfWeek > 5 ? 1 : $dayOfWeek;
        $dayName = TimetableEntry::DAYS[$dayOfWeek];
        $todaySchedule = $lessons->where('day_of_week', $dayOfWeek)->with('teacher')->orderBy('period')->get()->groupBy('class_name');

        return view('dashboard', compact('studentCount', 'teacherCount', 'gradesCount', 'averageGrade', 'classes', 'upcomingExams', 'recentGrades', 'dayName', 'todaySchedule'));
    }
}
