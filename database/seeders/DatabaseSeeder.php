<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TimetableEntry;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Ausführen mit: php artisan migrate:fresh --seed
     * Alle Logins haben das Passwort "demo1234".
     */
    public function run(): void
    {
        // 1. Admin-Konto
        User::create([
            'name' => 'Schulverwaltung',
            'email' => 'admin@schule.de',
            'password' => 'demo1234',
            'role' => 'admin',
        ]);

        // 2. Lehrkräfte
        $schmidt = Teacher::create(['name' => 'Anna Schmidt', 'code' => 'SCH', 'email' => 'schmidt@schule.de', 'phone' => '0711 123401', 'subjects' => 'Mathematik, Physik']);
        $mueller = Teacher::create(['name' => 'Thomas Müller', 'code' => 'MUE', 'email' => 'mueller@schule.de', 'phone' => '0711 123402', 'subjects' => 'Deutsch, Geschichte']);
        $weber = Teacher::create(['name' => 'Laura Weber', 'code' => 'WEB', 'email' => 'weber@schule.de', 'subjects' => 'Englisch, Französisch']);
        $fischer = Teacher::create(['name' => 'Jonas Fischer', 'code' => 'FIS', 'email' => 'fischer@schule.de', 'subjects' => 'Biologie, Chemie']);

        // Login für eine Lehrkraft
        User::create([
            'name' => $schmidt->name,
            'email' => 'schmidt@schule.de',
            'password' => 'demo1234',
            'role' => 'teacher',
            'teacher_id' => $schmidt->id,
        ]);

        // 3. Schüler in drei Klassen
        $students = [];
        $names = [
            '8a' => ['Sophie Keller', 'Leon Wagner', 'Mia Becker', 'Paul Hoffmann'],
            '9b' => ['Emma Schulz', 'Ben Koch', 'Lina Richter', 'Noah Wolf'],
            '10a' => ['Hannah Neumann', 'Elias Braun', 'Lea Zimmermann', 'Finn Krüger'],
        ];
        foreach ($names as $className => $classNames) {
            foreach ($classNames as $name) {
                $students[] = Student::create([
                    'name' => $name,
                    'class_name' => $className,
                    'email' => strtolower(str_replace(['ü', ' '], ['ue', '.'], $name)).'@schule.de',
                    'birthday' => now()->subYears(match ($className) { '8a' => 14, '9b' => 15, default => 16 })->subDays(rand(0, 360)),
                ]);
            }
        }

        // Login für einen Schüler (Sophie Keller, 8a)
        User::create([
            'name' => $students[0]->name,
            'email' => 'sophie@schule.de',
            'password' => 'demo1234',
            'role' => 'student',
            'student_id' => $students[0]->id,
        ]);

        // 4. Arbeiten – zwei geschrieben, drei in der Zukunft
        $mathe8a = Exam::create(['title' => 'Lineare Funktionen', 'subject' => 'Mathematik', 'class_name' => '8a', 'teacher_id' => $schmidt->id, 'exam_date' => now()->subDays(10), 'type' => 'Klassenarbeit', 'weighting' => 2.0, 'status' => 'completed', 'topics' => 'Steigung, y-Achsenabschnitt, Geraden zeichnen']);
        $deutsch9b = Exam::create(['title' => 'Erörterung', 'subject' => 'Deutsch', 'class_name' => '9b', 'teacher_id' => $mueller->id, 'exam_date' => now()->subDays(4), 'type' => 'Klassenarbeit', 'weighting' => 2.0, 'status' => 'completed']);
        Exam::create(['title' => 'Vokabeltest Unit 3', 'subject' => 'Englisch', 'class_name' => '8a', 'teacher_id' => $weber->id, 'exam_date' => now()->addDays(5), 'type' => 'Kurzarbeit/Test', 'weighting' => 1.0, 'status' => 'planned']);
        Exam::create(['title' => 'Zellbiologie', 'subject' => 'Biologie', 'class_name' => '10a', 'teacher_id' => $fischer->id, 'exam_date' => now()->addDays(8), 'type' => 'Klassenarbeit', 'weighting' => 2.0, 'status' => 'planned', 'topics' => 'Zellaufbau, Mitose']);
        Exam::create(['title' => 'Quadratische Gleichungen', 'subject' => 'Mathematik', 'class_name' => '9b', 'teacher_id' => $schmidt->id, 'exam_date' => now()->addDays(12), 'type' => 'Klassenarbeit', 'weighting' => 2.0, 'status' => 'planned']);

        // 5. Noten – zu den geschriebenen Arbeiten und ein paar mündliche
        $examNotes = [$mathe8a, $deutsch9b];
        foreach ($students as $student) {
            foreach ($examNotes as $exam) {
                if ($exam->class_name === $student->class_name) {
                    Grade::create([
                        'student_id' => $student->id,
                        'exam_id' => $exam->id,
                        'student_name' => $student->name,
                        'class_name' => $student->class_name,
                        'subject' => $exam->subject,
                        'grade' => rand(10, 50) / 10,
                        'assessment' => $exam->type.': '.$exam->title,
                    ]);
                }
            }

            Grade::create([
                'student_id' => $student->id,
                'student_name' => $student->name,
                'class_name' => $student->class_name,
                'subject' => 'Englisch',
                'grade' => rand(10, 40) / 10,
                'assessment' => 'Mündliche Note',
            ]);
        }

        // 6. Stundenplan – Montag bis Freitag, damit auch "heute" etwas angezeigt wird
        $lessons = [
            '8a' => [['Mathematik', $schmidt, 'R 101', 'mint'], ['Deutsch', $mueller, 'R 101', 'sky'], ['Englisch', $weber, 'R 104', 'purple'], ['Biologie', $fischer, 'Bio 1', 'coral']],
            '9b' => [['Deutsch', $mueller, 'R 202', 'sky'], ['Mathematik', $schmidt, 'R 202', 'mint'], ['Chemie', $fischer, 'Chem 1', 'yellow'], ['Englisch', $weber, 'R 205', 'purple']],
            '10a' => [['Englisch', $weber, 'R 301', 'purple'], ['Biologie', $fischer, 'Bio 1', 'coral'], ['Physik', $schmidt, 'Phy 1', 'yellow'], ['Geschichte', $mueller, 'R 301', 'sky']],
        ];
        foreach ($lessons as $className => $classLessons) {
            foreach (TimetableEntry::DAYS as $day => $dayName) {
                // Jeden Tag 4 Stunden, Fächer pro Tag versetzt
                for ($period = 1; $period <= 4; $period++) {
                    [$subject, $teacher, $room, $color] = $classLessons[($day + $period) % count($classLessons)];
                    TimetableEntry::create([
                        'class_name' => $className,
                        'subject' => $subject,
                        'teacher_id' => $teacher->id,
                        'day_of_week' => $day,
                        'period' => $period,
                        'time_start' => TimetableEntry::PERIODS[$period]['start'],
                        'time_end' => TimetableEntry::PERIODS[$period]['end'],
                        'room' => $room,
                        'color' => $color,
                    ]);
                }
            }
        }
    }
}
