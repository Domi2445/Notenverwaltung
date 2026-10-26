<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\Student;

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
        $classFilter = $request->query('class');
        $subjectFilter = $request->query('subject');
        $search = $request->query('search');

        $user = $request->user();
        $visibleClasses = $user->visibleClasses();
        $query = Grade::query();

        // Schüler sehen nur ihre eigenen Noten (ohne verknüpften Schüler: gar keine),
        // Lehrkräfte nur die Noten ihrer Klassen
        if ($user->isStudent()) {
            $query->where('student_id', $user->student_id ?? 0);
        } elseif ($visibleClasses !== null) {
            $query->whereIn('class_name', $visibleClasses);
        }

        if($classFilter) {
            $query->where('class_name', $classFilter);
        }
        if($subjectFilter) {
            $query->where('subject', $subjectFilter);
        }
        if($search) {
            $query->where('student_name', 'like', "%{$search}%");
        }

        $grades = $query->latest()->get();

        $totalCount = $grades->count();
        $averageGrade = $grades->isEmpty() ? null : round($grades->avg('grade'), 2);

        $distribution = [1=>0,2=>0,3=>0,4=>0,5=>0,6=>0];

        foreach($grades as $grade) {
            $stufe = (int) ceil($grade->grade - 0.5);
            $distribution[$stufe]++;
        }

        if ($user->isStudent()) {
            // Filter nur aus den eigenen Noten, keine Listen anderer Schüler
            $classes = $grades->pluck('class_name')->unique()->sort()->values();
            $subjects = $grades->pluck('subject')->unique()->sort()->values();
            $students = collect();
            $exams = collect();
        } elseif ($visibleClasses !== null) {
            // Lehrkraft: nur ihre Klassen, nur Schüler und Arbeiten dieser Klassen
            $classes = $visibleClasses->sort()->values();
            $subjects = Grade::whereIn('class_name', $visibleClasses)->pluck('subject')->unique()->sort()->values();
            $students = Student::whereIn('class_name', $visibleClasses)->orderBy('class_name')->orderBy('name')->get();
            $exams = Exam::whereIn('class_name', $visibleClasses)->latest('exam_date')->get();
        } else {
            $classes = Grade::pluck('class_name')->merge(Student::pluck('class_name'))->unique()->sort()->values();
            $subjects = Grade::pluck('subject')->unique()->sort()->values();
            $students = Student::orderBy('class_name')->orderBy('name')->get();
            $exams = Exam::latest('exam_date')->get();
        }

        return view('grades.index', [
            'grades' => $grades,
            'totalCount' => $totalCount,
            'averageGrade' => $averageGrade,
            'distribution' => $distribution,
            'classFilter' => $classFilter,
            'subjectFilter' => $subjectFilter,
            'search' => $search,
            'classes' => $classes,
            'subjects' => $subjects,
            'students' => $students,
            'exams' => $exams,
        ]);
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

        $validated = $request->validate([
            'student_id' => ['nullable', 'exists:students,id'],
            'exam_id' => ['nullable', 'exists:exams,id'],
            'student_name' => ['nullable'],
            'class_name' => ['nullable'],
            'subject' => ['required'],
            'grade' => ['required', 'numeric', 'between:1,6'],
            'assessment' => ['nullable'],
            'comment' => ['nullable'],
        ]);

        // Schüler ausgewählt? Dann Name und Klasse vom Schüler übernehmen
        if ($validated['student_id'] ?? null) {
            $student = Student::find($validated['student_id']);
            $validated['student_name'] = $student->name;
            $validated['class_name'] = ($validated['class_name'] ?? null) ?: $student->class_name;
        }

        if (empty($validated['student_name'])) {
            return back()->withErrors(['student_name' => 'Bitte einen Schüler auswählen oder einen Namen eingeben.'])->withInput();
        }

        if (empty($validated['class_name'])) {
            return back()->withErrors(['class_name' => 'Bitte eine Klasse angeben.'])->withInput();
        }

        // Lehrkräfte dürfen nur in ihren eigenen Klassen Noten eintragen
        if (! $request->user()->canAccessClass($validated['class_name'])) {
            return back()->withErrors(['class_name' => 'Du unterrichtest die Klasse '.$validated['class_name'].' nicht.'])->withInput();
        }

        Grade::create($validated);

        return redirect()->route('grades.index')->with('status', 'Die Note wurde gespeichert.');
    }

    // DELETE /noten/{grade} – Route "grades.destroy"
    public function destroy(Request $request, Grade $grade)
    {
        // TODO: Note löschen und mit Meldung zurück -> back()->with('status', '...')
        // Warum back() statt route()? Damit die gesetzten Filter (z. B. ?class=8a) erhalten bleiben.

        abort_unless($request->user()->canAccessClass($grade->class_name), 403);

        $grade->delete();

        return back()->with('status', 'Die Note wurde gelöscht.');
    }
}
