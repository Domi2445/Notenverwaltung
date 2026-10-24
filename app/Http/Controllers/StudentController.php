<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    // GET /schueler – Route "students.index" (siehe docs/PLAN.md, Schritt 4)
    public function index(Request $request)
    {
        //
    }

    // POST /schueler – Route "students.store"
    public function store(Request $request)
    {
        //
    }

    // DELETE /schueler/{student} – Route "students.destroy"
    public function destroy(Student $student)
    {
        //
    }
}
