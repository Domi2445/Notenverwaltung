<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    // GET /lehrer – Route "teachers.index" (siehe docs/PLAN.md, Schritt 5)
    public function index(Request $request)
    {
        //
    }

    // POST /lehrer – Route "teachers.store"
    public function store(Request $request)
    {
        //
    }

    // DELETE /lehrer/{teacher} – Route "teachers.destroy"
    public function destroy(Teacher $teacher)
    {
        //
    }
}
