<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\TimetableEntry;
use App\Services\SchoolDemoDataSeeder;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->user()->organizations()->firstOrFail();

        if ($organization->slug === 'nexora-demo') {
            SchoolDemoDataSeeder::seed($organization);
        }

        $classes = $organization->timetableEntries()->pluck('class_name')
            ->merge($organization->students()->pluck('class_name'))
            ->unique()->filter()->sort()->values();

        $selectedClass = $request->query('class') ?: ($classes->first() ?: '8a');
        $teacherFilter = $request->query('teacher_id');

        $query = $organization->timetableEntries()->with('teacher');

        if ($teacherFilter) {
            $query->where('teacher_id', $teacherFilter);
        } elseif ($selectedClass) {
            $query->where('class_name', $selectedClass);
        }

        $entries = $query->get();

        // Organize entries by day (1..5) and period (1..8)
        $grid = [];
        for ($period = 1; $period <= 8; $period++) {
            for ($day = 1; $day <= 5; $day++) {
                $grid[$period][$day] = $entries->first(fn ($e) => $e->day_of_week === $day && $e->period === $period);
            }
        }

        $teachers = $organization->teachers()->orderBy('name')->get();
        $periods = TimetableEntry::$defaultPeriods;
        $days = TimetableEntry::$days;

        return view('modules.timetable.index', compact(
            'organization',
            'classes',
            'selectedClass',
            'teacherFilter',
            'teachers',
            'grid',
            'periods',
            'days',
            'entries'
        ));
    }

    public function store(Request $request)
    {
        $organization = $request->user()->organizations()->firstOrFail();

        $validated = $request->validate([
            'class_name' => ['required', 'string', 'max:50'],
            'day_of_week' => ['required', 'integer', 'between:1,5'],
            'period' => ['required', 'integer', 'between:1,8'],
            'subject' => ['required', 'string', 'max:80'],
            'room' => ['nullable', 'string', 'max:30'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'color' => ['nullable', 'string', 'in:coral,mint,yellow,sky,purple'],
            'time_start' => ['nullable', 'string', 'max:10'],
            'time_end' => ['nullable', 'string', 'max:10'],
        ]);

        $defaultTimes = TimetableEntry::$defaultPeriods[$validated['period']] ?? ['start' => '08:00', 'end' => '08:45'];
        $timeStart = $validated['time_start'] ?? $defaultTimes['start'];
        $timeEnd = $validated['time_end'] ?? $defaultTimes['end'];
        $color = $validated['color'] ?? 'mint';

        // Update or create entry at that slot for that class
        $organization->timetableEntries()->updateOrCreate(
            [
                'class_name' => $validated['class_name'],
                'day_of_week' => $validated['day_of_week'],
                'period' => $validated['period'],
            ],
            [
                'subject' => $validated['subject'],
                'room' => $validated['room'],
                'teacher_id' => $validated['teacher_id'],
                'color' => $color,
                'time_start' => $timeStart,
                'time_end' => $timeEnd,
            ]
        );

        return to_route('timetable.index', ['class' => $validated['class_name']])
            ->with('status', "Stunde {$validated['period']} ({$validated['subject']}) für Klasse {$validated['class_name']} gespeichert.");
    }

    public function destroy(Request $request, TimetableEntry $entry)
    {
        $organization = $request->user()->organizations()->firstOrFail();
        abort_unless($entry->organization_id === $organization->id, 403);

        $className = $entry->class_name;
        $entry->delete();

        return to_route('timetable.index', ['class' => $className])
            ->with('status', 'Stundeneintrag wurde entfernt.');
    }
}
