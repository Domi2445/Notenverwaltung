<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // TODO: Testdaten anlegen (siehe docs/PLAN.md, Schritt 10)
        // Ausführen mit: php artisan migrate:fresh --seed
        //
        // 1. Ein Benutzerkonto zum Anmelden -> User::create([... 'password' => 'demo1234'])
        // 2. 3–5 Lehrkräfte -> Teacher::create([...])
        //    Die Rückgabe in Variablen speichern, z. B. $schmidt = Teacher::create(...),
        //    damit du ihre id später für Arbeiten und Stunden verwenden kannst.
        // 3. Schüler in 2–3 Klassen (z. B. 8a, 9b, 10a)
        // 4. Ein paar Arbeiten, mindestens eine in der Zukunft (damit die Übersicht etwas zeigt)
        //    -> now()->addDays(5)
        // 5. Noten für einige Schüler, mit student_id, student_name und class_name
        // 6. Einige Stunden im Stundenplan, auch für den heutigen Wochentag
        //    Start/Ende aus TimetableEntry::PERIODS übernehmen.
        //
        // Tipp: Mit foreach über ein Array mit Daten sparst du viel Schreibarbeit.
    }
}
