<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Organization;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TimetableEntry;
use Carbon\Carbon;

class SchoolDemoDataSeeder
{
    public static function seed(Organization $organization): void
    {
        // Only seed if empty
        if ($organization->students()->exists() || $organization->teachers()->exists()) {
            return;
        }

        // 1. Lehrkräfte anlegen
        $teachersData = [
            ['name' => 'Dr. Martin Schmidt', 'code' => 'SCH', 'email' => 'schmidt@schule.local', 'subjects' => 'Mathematik, Physik'],
            ['name' => 'Sabine Meyer', 'code' => 'MEY', 'email' => 'meyer@schule.local', 'subjects' => 'Deutsch, Geschichte'],
            ['name' => 'Thomas Wagner', 'code' => 'WAG', 'email' => 'wagner@schule.local', 'subjects' => 'Englisch, Sport'],
            ['name' => 'Julia Becker', 'code' => 'BEC', 'email' => 'becker@schule.local', 'subjects' => 'Biologie, Chemie'],
            ['name' => 'Florian Weber', 'code' => 'WEB', 'email' => 'weber@schule.local', 'subjects' => 'Informatik, Kunst'],
        ];

        $teachers = [];
        foreach ($teachersData as $td) {
            $teachers[$td['code']] = Teacher::create([
                'organization_id' => $organization->id,
                ...$td,
            ]);
        }

        // 2. Schüler anlegen
        $studentsData = [
            ['name' => 'Lukas Weber', 'class_name' => '8a', 'email' => 'lukas.weber@schueler.local', 'birthday' => '2012-04-12'],
            ['name' => 'Sophie Keller', 'class_name' => '8a', 'email' => 'sophie.keller@schueler.local', 'birthday' => '2012-08-25'],
            ['name' => 'Noah Becker', 'class_name' => '8a', 'email' => 'noah.becker@schueler.local', 'birthday' => '2012-01-19'],
            ['name' => 'Mia Hofmann', 'class_name' => '8a', 'email' => 'mia.hofmann@schueler.local', 'birthday' => '2012-11-03'],
            ['name' => 'Leon Wagner', 'class_name' => '9b', 'email' => 'leon.wagner@schueler.local', 'birthday' => '2011-03-15'],
            ['name' => 'Emma Richter', 'class_name' => '9b', 'email' => 'emma.richter@schueler.local', 'birthday' => '2011-07-22'],
            ['name' => 'Felix Braun', 'class_name' => '9b', 'email' => 'felix.braun@schueler.local', 'birthday' => '2011-09-08'],
            ['name' => 'Hannah Klein', 'class_name' => '10a', 'email' => 'hannah.klein@schueler.local', 'birthday' => '2010-02-14'],
            ['name' => 'Julian Zimmermann', 'class_name' => '10a', 'email' => 'julian.z@schueler.local', 'birthday' => '2010-06-30'],
        ];

        $createdStudents = [];
        foreach ($studentsData as $sd) {
            $createdStudents[] = Student::create([
                'organization_id' => $organization->id,
                ...$sd,
            ]);
        }

        // 3. Stundenplan für Klasse 8a (Montag bis Freitag, Perioden 1-6)
        $schedule8a = [
            // Montag
            ['day' => 1, 'period' => 1, 'subject' => 'Mathematik', 'room' => 'R104', 'teacher' => 'SCH', 'color' => 'coral'],
            ['day' => 1, 'period' => 2, 'subject' => 'Mathematik', 'room' => 'R104', 'teacher' => 'SCH', 'color' => 'coral'],
            ['day' => 1, 'period' => 3, 'subject' => 'Deutsch', 'room' => 'R104', 'teacher' => 'MEY', 'color' => 'mint'],
            ['day' => 1, 'period' => 4, 'subject' => 'Englisch', 'room' => 'R104', 'teacher' => 'WAG', 'color' => 'yellow'],
            ['day' => 1, 'period' => 5, 'subject' => 'Biologie', 'room' => 'Bio-1', 'teacher' => 'BEC', 'color' => 'sky'],
            ['day' => 1, 'period' => 6, 'subject' => 'Geschichte', 'room' => 'R104', 'teacher' => 'MEY', 'color' => 'purple'],

            // Dienstag
            ['day' => 2, 'period' => 1, 'subject' => 'Englisch', 'room' => 'R104', 'teacher' => 'WAG', 'color' => 'yellow'],
            ['day' => 2, 'period' => 2, 'subject' => 'Deutsch', 'room' => 'R104', 'teacher' => 'MEY', 'color' => 'mint'],
            ['day' => 2, 'period' => 3, 'subject' => 'Informatik', 'room' => 'Info-2', 'teacher' => 'WEB', 'color' => 'sky'],
            ['day' => 2, 'period' => 4, 'subject' => 'Informatik', 'room' => 'Info-2', 'teacher' => 'WEB', 'color' => 'sky'],
            ['day' => 2, 'period' => 5, 'subject' => 'Sport', 'room' => 'TH-1', 'teacher' => 'WAG', 'color' => 'coral'],
            ['day' => 2, 'period' => 6, 'subject' => 'Sport', 'room' => 'TH-1', 'teacher' => 'WAG', 'color' => 'coral'],

            // Mittwoch
            ['day' => 3, 'period' => 1, 'subject' => 'Mathematik', 'room' => 'R104', 'teacher' => 'SCH', 'color' => 'coral'],
            ['day' => 3, 'period' => 2, 'subject' => 'Physik', 'room' => 'Phy-1', 'teacher' => 'SCH', 'color' => 'purple'],
            ['day' => 3, 'period' => 3, 'subject' => 'Deutsch', 'room' => 'R104', 'teacher' => 'MEY', 'color' => 'mint'],
            ['day' => 3, 'period' => 4, 'subject' => 'Kunst', 'room' => 'Kun-1', 'teacher' => 'WEB', 'color' => 'yellow'],
            ['day' => 3, 'period' => 5, 'subject' => 'Kunst', 'room' => 'Kun-1', 'teacher' => 'WEB', 'color' => 'yellow'],

            // Donnerstag
            ['day' => 4, 'period' => 1, 'subject' => 'Geschichte', 'room' => 'R104', 'teacher' => 'MEY', 'color' => 'purple'],
            ['day' => 4, 'period' => 2, 'subject' => 'Englisch', 'room' => 'R104', 'teacher' => 'WAG', 'color' => 'yellow'],
            ['day' => 4, 'period' => 3, 'subject' => 'Mathematik', 'room' => 'R104', 'teacher' => 'SCH', 'color' => 'coral'],
            ['day' => 4, 'period' => 4, 'subject' => 'Chemie', 'room' => 'Ch-1', 'teacher' => 'BEC', 'color' => 'mint'],
            ['day' => 4, 'period' => 5, 'subject' => 'Chemie', 'room' => 'Ch-1', 'teacher' => 'BEC', 'color' => 'mint'],

            // Freitag
            ['day' => 5, 'period' => 1, 'subject' => 'Deutsch', 'room' => 'R104', 'teacher' => 'MEY', 'color' => 'mint'],
            ['day' => 5, 'period' => 2, 'subject' => 'Deutsch', 'room' => 'R104', 'teacher' => 'MEY', 'color' => 'mint'],
            ['day' => 5, 'period' => 3, 'subject' => 'Englisch', 'room' => 'R104', 'teacher' => 'WAG', 'color' => 'yellow'],
            ['day' => 5, 'period' => 4, 'subject' => 'Biologie', 'room' => 'Bio-1', 'teacher' => 'BEC', 'color' => 'sky'],
            ['day' => 5, 'period' => 5, 'subject' => 'Physik', 'room' => 'Phy-1', 'teacher' => 'SCH', 'color' => 'purple'],
        ];

        foreach ($schedule8a as $item) {
            $periodTimes = TimetableEntry::$defaultPeriods[$item['period']] ?? ['start' => '08:00', 'end' => '08:45'];
            TimetableEntry::create([
                'organization_id' => $organization->id,
                'class_name' => '8a',
                'day_of_week' => $item['day'],
                'period' => $item['period'],
                'time_start' => $periodTimes['start'],
                'time_end' => $periodTimes['end'],
                'subject' => $item['subject'],
                'room' => $item['room'],
                'teacher_id' => $teachers[$item['teacher']]->id ?? null,
                'color' => $item['color'],
            ]);
        }

        // Stundenplan auch für 9b
        $schedule9b = [
            ['day' => 1, 'period' => 1, 'subject' => 'Deutsch', 'room' => 'R201', 'teacher' => 'MEY', 'color' => 'mint'],
            ['day' => 1, 'period' => 2, 'subject' => 'Englisch', 'room' => 'R201', 'teacher' => 'WAG', 'color' => 'yellow'],
            ['day' => 1, 'period' => 3, 'subject' => 'Mathematik', 'room' => 'R201', 'teacher' => 'SCH', 'color' => 'coral'],
            ['day' => 2, 'period' => 1, 'subject' => 'Informatik', 'room' => 'Info-1', 'teacher' => 'WEB', 'color' => 'sky'],
            ['day' => 2, 'period' => 2, 'subject' => 'Physik', 'room' => 'Phy-1', 'teacher' => 'SCH', 'color' => 'purple'],
            ['day' => 3, 'period' => 1, 'subject' => 'Biologie', 'room' => 'Bio-1', 'teacher' => 'BEC', 'color' => 'mint'],
            ['day' => 4, 'period' => 1, 'subject' => 'Mathematik', 'room' => 'R201', 'teacher' => 'SCH', 'color' => 'coral'],
            ['day' => 5, 'period' => 1, 'subject' => 'Sport', 'room' => 'TH-2', 'teacher' => 'WAG', 'color' => 'coral'],
        ];
        foreach ($schedule9b as $item) {
            $periodTimes = TimetableEntry::$defaultPeriods[$item['period']] ?? ['start' => '08:00', 'end' => '08:45'];
            TimetableEntry::create([
                'organization_id' => $organization->id,
                'class_name' => '9b',
                'day_of_week' => $item['day'],
                'period' => $item['period'],
                'time_start' => $periodTimes['start'],
                'time_end' => $periodTimes['end'],
                'subject' => $item['subject'],
                'room' => $item['room'],
                'teacher_id' => $teachers[$item['teacher']]->id ?? null,
                'color' => $item['color'],
            ]);
        }

        // 4. Arbeiten & Prüfungen
        $exam1 = Exam::create([
            'organization_id' => $organization->id,
            'title' => '1. Klassenarbeit: Lineare Funktionen & Gleichungssysteme',
            'subject' => 'Mathematik',
            'class_name' => '8a',
            'teacher_id' => $teachers['SCH']->id,
            'exam_date' => Carbon::now()->subDays(5),
            'type' => 'Klassenarbeit',
            'weighting' => 2.0,
            'status' => 'completed',
            'topics' => 'Steigungsdreiecke, Schnittpunkte zweier Geraden, Einsetzungsverfahren',
        ]);

        $exam2 = Exam::create([
            'organization_id' => $organization->id,
            'title' => 'Vokabel- & Grammatiktest Unit 3',
            'subject' => 'Englisch',
            'class_name' => '8a',
            'teacher_id' => $teachers['WAG']->id,
            'exam_date' => Carbon::now()->addDays(3),
            'type' => 'Kurzarbeit/Test',
            'weighting' => 1.0,
            'status' => 'planned',
            'topics' => 'Past Perfect vs Simple Past, Vokabeln S. 45-48',
        ]);

        $exam3 = Exam::create([
            'organization_id' => $organization->id,
            'title' => 'Klausur: Textanalyse und Erörterung',
            'subject' => 'Deutsch',
            'class_name' => '10a',
            'teacher_id' => $teachers['MEY']->id,
            'exam_date' => Carbon::now()->addDays(10),
            'type' => 'Klausur',
            'weighting' => 2.0,
            'status' => 'planned',
            'topics' => 'Sachtextanalyse, Argumentationsaufbau, dialektische Erörterung',
        ]);

        // 5. Noten eintragen
        $students8a = array_filter($createdStudents, fn ($s) => $s->class_name === '8a');
        $sampleGrades = [
            ['grade' => 1.7, 'assessment' => 'Klassenarbeit 1', 'comment' => 'Hervorragende Bearbeitung des Beweisteils'],
            ['grade' => 2.3, 'assessment' => 'Klassenarbeit 1', 'comment' => 'Gute Ansätze, kleine Rechenfehler im Gleichungssystem'],
            ['grade' => 1.0, 'assessment' => 'Klassenarbeit 1', 'comment' => 'Volle Punktzahl, fehlerfreie Darstellung'],
            ['grade' => 3.0, 'assessment' => 'Klassenarbeit 1', 'comment' => 'Zeichnung ungenau, Regeln verstanden'],
        ];

        $i = 0;
        foreach ($students8a as $student) {
            $g = $sampleGrades[$i % count($sampleGrades)];
            Grade::create([
                'organization_id' => $organization->id,
                'student_id' => $student->id,
                'exam_id' => $exam1->id,
                'student_name' => $student->name,
                'class_name' => $student->class_name,
                'subject' => 'Mathematik',
                'grade' => $g['grade'],
                'assessment' => $g['assessment'],
                'comment' => $g['comment'],
            ]);

            // Mündliche Note dazu
            Grade::create([
                'organization_id' => $organization->id,
                'student_id' => $student->id,
                'student_name' => $student->name,
                'class_name' => $student->class_name,
                'subject' => 'Deutsch',
                'grade' => round(1.0 + (mt_rand(0, 30) / 10), 1),
                'assessment' => 'Mündliche Mitarbeit',
                'comment' => 'Regelmäßige, fundierte Beiträge im Unterricht',
            ]);
            $i++;
        }
    }
}
