<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Services\SchoolDemoDataSeeder;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->user()->organizations()->firstOrFail();

        if ($organization->slug === 'nexora-demo') {
            SchoolDemoDataSeeder::seed($organization);
        }

        $query = $organization->teachers()->withCount(['timetableEntries', 'exams']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('subjects', 'like', "%{$search}%");
            });
        }

        $teachers = $query->orderBy('name')->get();

        return view('modules.teachers.index', compact('organization', 'teachers', 'search'));
    }

    public function store(Request $request)
    {
        $organization = $request->user()->organizations()->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'code' => ['required', 'string', 'max:10'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subjects' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $organization->teachers()->create($validated);

        return to_route('teachers.index')->with('status', "Lehrkraft {$validated['name']} ({$validated['code']}) wurde eingetragen.");
    }

    public function destroy(Request $request, Teacher $teacher)
    {
        $organization = $request->user()->organizations()->firstOrFail();
        abort_unless($teacher->organization_id === $organization->id, 403);

        $name = $teacher->name;
        $teacher->delete();

        return to_route('teachers.index')->with('status', "Lehrkraft {$name} wurde gelöscht.");
    }
}
