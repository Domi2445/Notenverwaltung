<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Student;
use App\Services\SchoolDemoDataSeeder;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->user()->organizations()->firstOrFail();

        if ($organization->slug === 'nexora-demo') {
            SchoolDemoDataSeeder::seed($organization);
        }

        $query = $organization->grades()->with(['student', 'exam']);

        if ($classFilter = $request->query('class')) {
            $query->where('class_name', $classFilter);
        }

        if ($subjectFilter = $request->query('subject')) {
            $query->where('subject', $subjectFilter);
        }

        if ($search = $request->query('search')) {
            $query->where('student_name', 'like', "%{$search}%");
        }

        $grades = $query->latest()->get();

        // Statistics
        $totalCount = $grades->count();
        $averageGrade = $totalCount > 0 ? round((float) $grades->avg('grade'), 2) : null;

        // Grade distribution (1 to 6)
        $distribution = [
            1 => $grades->filter(fn ($g) => $g->grade >= 1.0 && $g->grade <= 1.5)->count(),
            2 => $grades->filter(fn ($g) => $g->grade > 1.5 && $g->grade <= 2.5)->count(),
            3 => $grades->filter(fn ($g) => $g->grade > 2.5 && $g->grade <= 3.5)->count(),
            4 => $grades->filter(fn ($g) => $g->grade > 3.5 && $g->grade <= 4.5)->count(),
            5 => $grades->filter(fn ($g) => $g->grade > 4.5 && $g->grade <= 5.5)->count(),
            6 => $grades->filter(fn ($g) => $g->grade > 5.5 && $g->grade <= 6.0)->count(),
        ];

        $classes = $organization->grades()->pluck('class_name')
            ->merge($organization->students()->pluck('class_name'))
            ->unique()->filter()->sort()->values();

        $subjects = $organization->grades()->pluck('subject')->unique()->filter()->sort()->values();
        $students = $organization->students()->orderBy('class_name')->orderBy('name')->get();
        $exams = $organization->exams()->orderBy('exam_date', 'desc')->get();

        return view('modules.grades.index', compact(
            'organization',
            'grades',
            'totalCount',
            'averageGrade',
            'distribution',
            'classes',
            'subjects',
            'students',
            'exams',
            'classFilter',
            'subjectFilter',
            'search'
        ));
    }

    public function store(Request $request)
    {
        $organization = $request->user()->organizations()->firstOrFail();

        $validated = $request->validate([
            'student_id' => ['nullable', 'exists:students,id'],
            'exam_id' => ['nullable', 'exists:exams,id'],
            'student_name' => ['nullable', 'string', 'max:120'],
            'class_name' => ['nullable', 'string', 'max:80'],
            'subject' => ['required', 'string', 'max:80'],
            'grade' => ['required', 'numeric', 'min:1', 'max:6'],
            'assessment' => ['nullable', 'string', 'max:120'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $studentName = $validated['student_name'] ?? null;
        $className = $validated['class_name'] ?? null;

        if (! empty($validated['student_id'])) {
            $student = $organization->students()->find($validated['student_id']);
            if ($student) {
                $studentName = $student->name;
                $className = $className ?: $student->class_name;
            }
        }

        if (empty($studentName)) {
            return back()->withErrors(['student_name' => 'Bitte einen Schülernamen angeben oder auswählen.'])->withInput();
        }

        if (empty($className)) {
            $className = 'Klasse';
        }

        Grade::create([
            'organization_id' => $organization->id,
            'student_id' => $validated['student_id'] ?? null,
            'exam_id' => $validated['exam_id'] ?? null,
            'student_name' => $studentName,
            'class_name' => $className,
            'subject' => $validated['subject'],
            'grade' => $validated['grade'],
            'assessment' => $validated['assessment'] ?? null,
            'comment' => $validated['comment'] ?? null,
        ]);

        return to_route('grades.index')->with('status', 'Die Note wurde erfolgreich gespeichert.');
    }

    public function destroy(Request $request, Grade $grade)
    {
        $organization = $request->user()->organizations()->firstOrFail();
        abort_unless($grade->organization_id === $organization->id, 403);

        $grade->delete();

        return to_route('grades.index')->with('status', 'Note wurde gelöscht.');
    }
}
