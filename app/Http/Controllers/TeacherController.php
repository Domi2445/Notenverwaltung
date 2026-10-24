<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    // GET /lehrer – Route "teachers.index" (siehe docs/PLAN.md, Schritt 5)
    public function index(Request $request)
    {
        // TODO:
        // 1. Suchtext aus der URL lesen -> $request->query('search')
        // 2. Abfrage mit Zählern starten: Die Oberfläche zeigt, wie viele Stunden und Arbeiten
        //    jede Lehrkraft hat -> Teacher::withCount(['timetableEntries', 'exams'])
        //    Danach gibt es automatisch $teacher->timetable_entries_count und $teacher->exams_count.
        // 3. Wenn gesucht wird: Name ODER Kürzel ODER Fächer enthält den Text (wie bei Schülern)
        // 4. Nach Name sortieren und laden
        // 5. View "teachers.index" mit teachers und search zurückgeben
    }

    // POST /lehrer – Route "teachers.store"
    public function store(Request $request)
    {
        // TODO:
        // 1. Eingaben prüfen:
        //    - name: Pflicht
        //    - code (Kürzel): Pflicht, max. 10 Zeichen, darf nur einmal vorkommen -> 'unique:teachers,code'
        //    - email (optional, E-Mail-Format), phone, subjects, notes: optional
        // 2. Tipp: Kürzel einheitlich in Großbuchstaben speichern -> strtoupper(...)
        // 3. Speichern und mit Meldung zurück zur Liste
    }

    // DELETE /lehrer/{teacher} – Route "teachers.destroy"
    public function destroy(Teacher $teacher)
    {
        // TODO: Löschen und mit Meldung zurück zur Liste.
        // Was passiert dabei mit Arbeiten und Stunden dieser Lehrkraft? Das hast du in den
        // Migrationen festgelegt (nullOnDelete) – probiere es aus!
    }
}
