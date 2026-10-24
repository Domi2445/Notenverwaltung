<?php

namespace App\Http\Controllers;

use App\Models\TimetableEntry;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    // GET /stundenplan – Route "timetable.index" (siehe docs/PLAN.md, Schritt 8)
    public function index(Request $request)
    {
        //
    }

    // POST /stundenplan – Route "timetable.store"
    public function store(Request $request)
    {
        //
    }

    // DELETE /stundenplan/{entry} – Route "timetable.destroy"
    public function destroy(TimetableEntry $entry)
    {
        //
    }
}
