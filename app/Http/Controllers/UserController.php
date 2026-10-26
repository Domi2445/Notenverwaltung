<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // POST /benutzer – Route "users.store"
    // Der Admin legt einen Login für eine Lehrkraft oder einen Schüler an
    // (nur Admin, siehe can:admin in routes/web.php)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'role' => ['required', 'in:teacher,student'],
            // Je nach Rolle muss die passende Person existieren
            'teacher_id' => ['required_if:role,teacher', 'nullable', 'exists:teachers,id', 'unique:users,teacher_id'],
            'student_id' => ['required_if:role,student', 'nullable', 'exists:students,id', 'unique:users,student_id'],
        ], [
            'teacher_id.unique' => 'Diese Lehrkraft hat schon einen Login.',
            'student_id.unique' => 'Diese/r Schüler/in hat schon einen Login.',
        ]);

        // Nur die ID speichern, die zur Rolle passt
        if ($validated['role'] === 'teacher') {
            $validated['student_id'] = null;
        } else {
            $validated['teacher_id'] = null;
        }

        User::create($validated);

        return back()->with('status', 'Login für '.$validated['name'].' wurde angelegt.');
    }
}
