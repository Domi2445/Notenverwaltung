<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Organization;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TimetableEntry;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->organization = Organization::create([
            'name' => 'Goethe-Gymnasium',
            'slug' => 'goethe-gymnasium',
            'status' => 'active',
        ]);
        $this->organization->users()->attach($this->user, ['role' => 'owner']);
    }

    public function test_school_dashboard_displays_kpis_and_overview(): void
    {
        $response = $this->actingAs($this->user)->get('/kundenbereich');

        $response->assertOk()
            ->assertSee('Schulportal')
            ->assertSee('Goethe-Gymnasium')
            ->assertSee('Schüler/innen')
            ->assertSee('Lehrkräfte')
            ->assertSee('Stundenplan');
    }

    public function test_students_can_be_listed_and_created(): void
    {
        $response = $this->actingAs($this->user)->get('/schueler');
        $response->assertOk()->assertSee('Schülerinnen &amp; Schüler', false);

        $postResponse = $this->actingAs($this->user)->post('/schueler', [
            'name' => 'Maximilian Mustermann',
            'class_name' => '9a',
            'email' => 'max.m@schule.local',
            'birthday' => '2011-05-10',
            'notes' => 'Klassensprecher',
        ]);

        $postResponse->assertRedirect('/schueler');

        $this->assertDatabaseHas('students', [
            'organization_id' => $this->organization->id,
            'name' => 'Maximilian Mustermann',
            'class_name' => '9a',
            'email' => 'max.m@schule.local',
        ]);

        $student = Student::where('email', 'max.m@schule.local')->first();
        $this->actingAs($this->user)->delete("/schueler/{$student->id}")
            ->assertRedirect('/schueler');

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_teachers_can_be_listed_and_created(): void
    {
        $response = $this->actingAs($this->user)->get('/lehrer');
        $response->assertOk()->assertSee('Lehrkräfte');

        $postResponse = $this->actingAs($this->user)->post('/lehrer', [
            'name' => 'Dr. Erika Sommer',
            'code' => 'SOM',
            'email' => 'sommer@schule.local',
            'subjects' => 'Biologie, Chemie',
            'phone' => '0151 9876543',
        ]);

        $postResponse->assertRedirect('/lehrer');

        $this->assertDatabaseHas('teachers', [
            'organization_id' => $this->organization->id,
            'name' => 'Dr. Erika Sommer',
            'code' => 'SOM',
        ]);

        $teacher = Teacher::where('code', 'SOM')->first();
        $this->actingAs($this->user)->delete("/lehrer/{$teacher->id}")
            ->assertRedirect('/lehrer');

        $this->assertDatabaseMissing('teachers', ['id' => $teacher->id]);
    }

    public function test_exams_can_be_planned_and_status_toggled(): void
    {
        $teacher = Teacher::create([
            'organization_id' => $this->organization->id,
            'name' => 'Herr Schulz',
            'code' => 'SLZ',
            'subjects' => 'Mathematik',
        ]);

        $response = $this->actingAs($this->user)->get('/arbeiten');
        $response->assertOk()->assertSee('Arbeiten &amp; Prüfungen', false);

        $postResponse = $this->actingAs($this->user)->post('/arbeiten', [
            'title' => '1. Klausur Analysis',
            'subject' => 'Mathematik',
            'class_name' => '10b',
            'teacher_id' => $teacher->id,
            'exam_date' => Carbon::now()->addDays(7)->format('Y-m-d'),
            'type' => 'Klausur',
            'weighting' => '2.0',
            'status' => 'planned',
            'topics' => 'Kurvendiskussion und Extremwertaufgaben',
        ]);

        $postResponse->assertRedirect('/arbeiten');

        $this->assertDatabaseHas('exams', [
            'organization_id' => $this->organization->id,
            'title' => '1. Klausur Analysis',
            'status' => 'planned',
        ]);

        $exam = Exam::where('title', '1. Klausur Analysis')->first();

        // Toggle status
        $this->actingAs($this->user)->post("/arbeiten/{$exam->id}/status");
        $this->assertSame('completed', $exam->fresh()->status);

        // Delete exam
        $this->actingAs($this->user)->delete("/arbeiten/{$exam->id}")
            ->assertRedirect('/arbeiten');
        $this->assertDatabaseMissing('exams', ['id' => $exam->id]);
    }

    public function test_timetable_can_be_scheduled_and_managed(): void
    {
        $teacher = Teacher::create([
            'organization_id' => $this->organization->id,
            'name' => 'Frau Hoffmann',
            'code' => 'HOF',
            'subjects' => 'Englisch',
        ]);

        $response = $this->actingAs($this->user)->get('/stundenplan');
        $response->assertOk()->assertSee('Stundenplan');

        $postResponse = $this->actingAs($this->user)->post('/stundenplan', [
            'class_name' => '8a',
            'day_of_week' => 2, // Dienstag
            'period' => 3,
            'subject' => 'Englisch',
            'room' => 'R204',
            'teacher_id' => $teacher->id,
            'color' => 'yellow',
        ]);

        $postResponse->assertRedirect('/stundenplan?class=8a');

        $this->assertDatabaseHas('timetable_entries', [
            'organization_id' => $this->organization->id,
            'class_name' => '8a',
            'day_of_week' => 2,
            'period' => 3,
            'subject' => 'Englisch',
            'room' => 'R204',
        ]);

        $entry = TimetableEntry::where('class_name', '8a')->where('day_of_week', 2)->first();
        $this->actingAs($this->user)->delete("/stundenplan/{$entry->id}")
            ->assertRedirect('/stundenplan?class=8a');

        $this->assertDatabaseMissing('timetable_entries', ['id' => $entry->id]);
    }

    public function test_grade_can_be_deleted(): void
    {
        $grade = Grade::create([
            'organization_id' => $this->organization->id,
            'student_name' => 'Tim Test',
            'class_name' => '7b',
            'subject' => 'Geschichte',
            'grade' => '2.0',
        ]);

        $this->actingAs($this->user)->delete("/notenverwaltung/noten/{$grade->id}")
            ->assertRedirect('/notenverwaltung');

        $this->assertDatabaseMissing('grades', ['id' => $grade->id]);
    }
}
