<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Teacher;
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

        $classFilter = $request->query('class');
        $statusFilter = $request->query('status');

        $visibleClasses = $request->user()->visibleClasses();

        // Grundabfrage: Schüler sehen nur die Arbeiten ihrer Klasse, Lehrkräfte die ihrer Klassen
        $base = fn () => $visibleClasses === null
            ? Exam::query()
            : Exam::whereIn('class_name', $visibleClasses);

        $query = $base()->with(['teacher', 'grades']);

        if ($classFilter) {
            $query->where('class_name', $classFilter);
        }
        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $exams = $query->orderBy('exam_date')->get();

        $plannedCount = $base()->where('status', 'planned')->count();
        $completedCount = $base()->where('status', 'completed')->count();
        $upcomingCount = $base()->where('status', 'planned')->where('exam_date', '>=', today())->count();

        $classes = $visibleClasses === null
            ? Student::pluck('class_name')->merge(Exam::pluck('class_name'))->unique()->sort()->values()
            : $visibleClasses->sort()->values();
        $teachers = Teacher::orderBy('name')->get();

        return view('exams.index', [
            'exams' => $exams,
            'classFilter' => $classFilter,
            'statusFilter' => $statusFilter,
            'plannedCount' => $plannedCount,
            'completedCount' => $completedCount,
            'upcomingCount' => $upcomingCount,
            'classes' => $classes,
            'teachers' => $teachers,
        ]);
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

        $validated = $request->validate(
            [
                'title' => 'required',
                'subject' => 'required',
                'class_name' => 'required',
                'exam_date' => 'required|date',
                'type' => 'required|in:Klassenarbeit,Kurzarbeit/Test,Klausur,Referat,Mündliche Prüfung',
                'weighting' => 'required|numeric|min:0.5|max:3',
                'teacher_id' => 'nullable|exists:teachers,id',
                'status' => 'required|in:planned,completed',
                'topics' => 'nullable',
            ]
        );

        $user = $request->user();

        // Lehrkräfte dürfen nur für ihre eigenen Klassen Arbeiten ansetzen
        if (! $user->canAccessClass($validated['class_name'])) {
            return back()->withErrors(['class_name' => 'Du unterrichtest die Klasse '.$validated['class_name'].' nicht.'])->withInput();
        }

        // Keine Lehrkraft ausgewählt? Dann die angemeldete Lehrkraft eintragen
        if ($user->isTeacher() && empty($validated['teacher_id'])) {
            $validated['teacher_id'] = $user->teacher_id;
        }

        Exam::create($validated);

        return redirect()->route('exams.index')->with('status', 'Arbeit erfolgreich erstellt.');
    }

    // POST /arbeiten/{exam}/status – Route "exams.status"
    public function updateStatus(Request $request, Exam $exam)
    {
        // TODO: Status umschalten: aus "planned" wird "completed" und umgekehrt.
        // -> Neuen Wert bestimmen (z. B. mit dem ternären Operator ? :), dann $exam->update([...])
        // Danach mit Meldung zurück.

        abort_unless($request->user()->canAccessClass($exam->class_name), 403);

        $exam->update([
            'status' => $exam->status === 'planned' ? 'completed' : 'planned',
        ]);

        return redirect()->route('exams.index')->with('status', 'Status erfolgreich geändert.');
    }

    // DELETE /arbeiten/{exam} – Route "exams.destroy"
    public function destroy(Request $request, Exam $exam)
    {
        // TODO: Arbeit löschen und mit Meldung zurück zur Liste.
        // Was passiert mit den Noten, die zu dieser Arbeit gehören? (Siehe deine grades-Migration.)

        abort_unless($request->user()->canAccessClass($exam->class_name), 403);
        $exam->delete();

        return redirect()->route('exams.index')->with('status', 'Arbeit erfolgreich gelöscht.');
    }
}
