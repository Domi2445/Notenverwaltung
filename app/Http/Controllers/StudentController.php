<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // GET /schueler – Route "students.index" (siehe docs/PLAN.md, Schritt 4)
    public function index(Request $request)
    {
        // TODO:
        // 1. Filter aus der URL lesen:
        //    $classFilter = $request->query('class');   (z. B. /schueler?class=8a)
        //    $search      = $request->query('search');
        // 2. Abfrage aufbauen und die Noten gleich mitladen (spart Datenbankabfragen)
        //    -> $query = Student::with('grades');
        // 3. Wenn $classFilter gesetzt ist: nur diese Klasse -> $query->where('class_name', ...)
        // 4. Wenn $search gesetzt ist: Name ODER E-Mail enthält den Suchtext
        //    -> where(function ($q) { $q->where('name', 'like', "%...%")->orWhere('email', ...); })
        //    Die Klammer-Funktion ist wichtig, damit das ODER nicht den Klassenfilter aushebelt.
        // 5. Sortieren (Klasse, dann Name) und laden -> orderBy(...)->get()
        // 6. $classes: alle vorhandenen Klassennamen für die Filter-Buttons
        //    -> Student::pluck('class_name')->unique()->sort()->values()
        // 7. View "students.index" mit students, classes, classFilter, search zurückgeben

        $classFilter = $request->query('class');
        $search = $request->query('search');

        // Lehrkräfte sehen nur die Schüler ihrer Klassen
        $visibleClasses = $request->user()->visibleClasses();

        $query = Student::with('grades');

        if ($visibleClasses !== null) {
            $query->whereIn('class_name', $visibleClasses);
        }
        if ($classFilter) {
            $query->where('class_name', $classFilter);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('class_name')->orderBy('name')->get();
        $classes = $visibleClasses === null
            ? Student::pluck('class_name')->unique()->sort()->values()
            : $visibleClasses->sort()->values();

        return view('students.index', compact('students', 'classes', 'classFilter', 'search'));
    }

    // POST /schueler – Route "students.store"
    public function store(Request $request)
    {
        // TODO:
        // 1. Eingaben prüfen:
        //    - name und class_name: Pflicht
        //    - email: optional, aber wenn angegeben im E-Mail-Format -> ['nullable', 'email']
        //    - birthday: optional, Datum
        //    - phone_number, address, notes: optional, Text
        //    -> $validated = $request->validate([...]);
        // 2. Schüler speichern -> Student::create($validated);
        // 3. Zurück zur Liste mit Erfolgsmeldung
        //    -> return redirect()->route('students.index')->with('status', '...');
        //    Die Meldung zeigt das Layout automatisch grün an.
        $validated = $request->validate([
            'name' => 'required',
            'class_name' => 'required',
            'email' => 'nullable|email',
            'birthday' => 'nullable|date',
            'phone_number' => 'nullable',
            'address' => 'nullable',
            'notes' => 'nullable',
        ]);
        Student::create($validated);
        return redirect()->route('students.index')->with('status', 'Schüler wurde angelegt');
    }

    // DELETE /schueler/{student} – Route "students.destroy"
    public function destroy(Student $student)
    {
        // TODO:
        // Laravel hat den Schüler über die ID in der URL schon geladen (Route Model Binding).
        // 1. Löschen -> $student->delete();
        // 2. Zurück zur Liste mit Meldung "… wurde gelöscht"
        $student->delete();
        return redirect()->route('students.index')->with('status', 'Schüler wurde gelöscht');
    }
}
