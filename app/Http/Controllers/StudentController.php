<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\SchoolDemoDataSeeder;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->user()->organizations()->firstOrFail();

        if ($organization->slug === 'nexora-demo') {
            SchoolDemoDataSeeder::seed($organization);
        }

        $query = $organization->students()->with(['grades' => fn($q) => $q->latest()]);

        if ($classFilter = $request->query('class')) {
            $query->where('class_name', $classFilter);
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('class_name')->orderBy('name')->get();
        $classes = $organization->students()->pluck('class_name')->unique()->sort()->values();

        return view('modules.students.index', compact('organization', 'students', 'classes', 'classFilter', 'search'));
    }

    public function store(Request $request)
    {
        $organization = $request->user()->organizations()->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'class_name' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'birthday' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $organization->students()->create($validated);

        return to_route('students.index')->with('status', "Schüler/in {$validated['name']} wurde erfolgreich angelegt.");
    }

    public function destroy(Request $request, Student $student)
    {
        $organization = $request->user()->organizations()->firstOrFail();
        abort_unless($student->organization_id === $organization->id, 403);

        $name = $student->name;
        $student->delete();

        return to_route('students.index')->with('status', "Schüler/in {$name} wurde gelöscht.");
    }
}
