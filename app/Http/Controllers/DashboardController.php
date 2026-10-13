<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TimetableEntry;
use App\Services\SchoolDemoDataSeeder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $organization = $request->user()->organizations()->with('subscriptions')->firstOrFail();

        // Seed demo data for demo organization or if completely empty
        if ($organization->slug === 'nexora-demo') {
            SchoolDemoDataSeeder::seed($organization);
        }

        $studentCount = $organization->students()->count();
        $teacherCount = $organization->teachers()->count();
        $gradesCount = $organization->grades()->count();
        $averageGrade = $organization->grades()->avg('grade');
        $averageGrade = $averageGrade !== null ? round((float) $averageGrade, 2) : null;

        // Upcoming exams
        $upcomingExams = $organization->exams()
            ->with('teacher')
            ->where('exam_date', '>=', Carbon::today())
            ->orderBy('exam_date', 'asc')
            ->take(5)
            ->get();

        // Recent grades
        $recentGrades = $organization->grades()
            ->with('student')
            ->latest()
            ->take(6)
            ->get();

        // Today's timetable (Monday=1 .. Friday=5, fallback to Monday if weekend)
        $currentDay = (int) Carbon::now()->dayOfWeekIso;
        if ($currentDay > 5) {
            $currentDay = 1; // Default to Monday on weekend
        }

        $todaySchedule = $organization->timetableEntries()
            ->with('teacher')
            ->where('day_of_week', $currentDay)
            ->orderBy('period')
            ->get()
            ->groupBy('class_name');

        $classes = $organization->students()->pluck('class_name')->unique()->filter()->values();
        if ($classes->isEmpty()) {
            $classes = $organization->timetableEntries()->pluck('class_name')->unique()->filter()->values();
        }

        return view('customer.dashboard', compact(
            'organization',
            'studentCount',
            'teacherCount',
            'gradesCount',
            'averageGrade',
            'upcomingExams',
            'recentGrades',
            'todaySchedule',
            'currentDay',
            'classes'
        ));
    }
}
