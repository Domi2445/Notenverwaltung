<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    // GET /arbeiten – Route "exams.index" (siehe docs/PLAN.md, Schritt 7)
    public function index(Request $request)
    {
        //
    }

    // POST /arbeiten – Route "exams.store"
    public function store(Request $request)
    {
        //
    }

    // POST /arbeiten/{exam}/status – Route "exams.status"
    public function updateStatus(Exam $exam)
    {
        //
    }

    // DELETE /arbeiten/{exam} – Route "exams.destroy"
    public function destroy(Exam $exam)
    {
        //
    }
}
