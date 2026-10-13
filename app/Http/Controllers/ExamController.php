<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Teacher;
use App\Services\SchoolDemoDataSeeder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->user()->organizations()->firstOrFail();

        if ($organization->slug === 'nexora-demo') {
            SchoolDemoDataSeeder::seed($organization);
        }

        $query = $organization->exams()->with(['teacher', 'grades']);

        if ($classFilter = $request->query('class')) {
            $query->where('class_name', $classFilter);
        }

        if ($statusFilter = $request->query('status')) {
            $query->where('status', $statusFilter);
        }

        $exams = $query->orderBy('exam_date', 'asc')->get();
        $teachers = $organization->teachers()->orderBy('name')->get();
        $classes = $organization->students()->pluck('class_name')->merge(
            $organization->exams()->pluck('class_name')
        )->unique()->filter()->sort()->values();

        $plannedCount = $organization->exams()->where('status', 'planned')->count();
        $completedCount = $organization->exams()->where('status', 'completed')->count();
        $upcomingCount = $organization->exams()
            ->where('status', 'planned')
            ->where('exam_date', '>=', Carbon::today())
            ->count();

        return view('modules.exams.index', compact(
            'organization',
            'exams',
            'teachers',
            'classes',
            'classFilter',
            'statusFilter',
            'plannedCount',
            'completedCount',
            'upcomingCount'
        ));
    }

    public function store(Request $request)
    {
        $organization = $request->user()->organizations()->firstOrFail();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'subject' => ['required', 'string', 'max:80'],
            'class_name' => ['required', 'string', 'max:50'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'exam_date' => ['required', 'date'],
            'type' => ['required', 'string', 'in:Klassenarbeit,Kurzarbeit/Test,Klausur,Referat,Mündliche Prüfung'],
            'weighting' => ['required', 'numeric', 'min:0.5', 'max:5.0'],
            'status' => ['required', 'string', 'in:planned,completed'],
            'topics' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $organization->exams()->create($validated);

        return to_route('exams.index')->with('status', "Arbeit \"{$validated['title']}\" wurde erfolgreich angelegt.");
    }

    public function updateStatus(Request $request, Exam $exam)
    {
        $organization = $request->user()->organizations()->firstOrFail();
        abort_unless($exam->organization_id === $organization->id, 403);

        $newStatus = $exam->status === 'planned' ? 'completed' : 'planned';
        $exam->update(['status' => $newStatus]);

        return back()->with('status', "Status der Arbeit aktualisiert auf: " . ($newStatus === 'completed' ? 'Abgeschlossen' : 'Geplant'));
    }

    public function destroy(Request $request, Exam $exam)
    {
        $organization = $request->user()->organizations()->firstOrFail();
        abort_unless($exam->organization_id === $organization->id, 403);

        $title = $exam->title;
        $exam->delete();

        return to_route('exams.index')->with('status', "Arbeit \"{$title}\" wurde gelöscht.");
    }
}
