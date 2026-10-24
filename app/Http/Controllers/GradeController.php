<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    // GET /noten – Route "grades.index" (siehe docs/PLAN.md, Schritt 6)
    public function index(Request $request)
    {
        //
    }

    // POST /noten – Route "grades.store"
    public function store(Request $request)
    {
        //
    }

    // DELETE /noten/{grade} – Route "grades.destroy"
    public function destroy(Grade $grade)
    {
        //
    }
}
