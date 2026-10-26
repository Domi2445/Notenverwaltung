<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Nur für Gäste (nicht angemeldet)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/registrieren', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registrieren', [AuthController::class, 'register'])->name('register.store');
});

// Nur für angemeldete Benutzer
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Ansehen dürfen alle angemeldeten Benutzer (Schüler sehen nur ihre eigenen Daten, siehe Controller)
    Route::get('/noten', [GradeController::class, 'index'])->name('grades.index');
    Route::get('/arbeiten', [ExamController::class, 'index'])->name('exams.index');
    Route::get('/stundenplan', [TimetableController::class, 'index'])->name('timetable.index');

    // Admin und Lehrkräfte: Schüler- und Lehrkräfteliste ansehen
    Route::middleware('can:view-people')->group(function () {
        Route::get('/schueler', [StudentController::class, 'index'])->name('students.index');
        Route::get('/lehrer', [TeacherController::class, 'index'])->name('teachers.index');
    });

    // Admin und Lehrkräfte: Noten und Arbeiten bearbeiten
    Route::middleware('can:edit-grades')->group(function () {
        Route::post('/noten', [GradeController::class, 'store'])->name('grades.store');
        Route::delete('/noten/{grade}', [GradeController::class, 'destroy'])->name('grades.destroy');

        Route::post('/arbeiten', [ExamController::class, 'store'])->name('exams.store');
        Route::post('/arbeiten/{exam}/status', [ExamController::class, 'updateStatus'])->name('exams.status');
        Route::delete('/arbeiten/{exam}', [ExamController::class, 'destroy'])->name('exams.destroy');
    });

    // Nur Admin: Schüler, Lehrkräfte, Stundenplan und Logins verwalten
    Route::middleware('can:admin')->group(function () {
        Route::post('/benutzer', [UserController::class, 'store'])->name('users.store');

        Route::post('/schueler', [StudentController::class, 'store'])->name('students.store');
        Route::delete('/schueler/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

        Route::post('/lehrer', [TeacherController::class, 'store'])->name('teachers.store');
        Route::delete('/lehrer/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');

        Route::post('/stundenplan', [TimetableController::class, 'store'])->name('timetable.store');
        Route::delete('/stundenplan/{entry}', [TimetableController::class, 'destroy'])->name('timetable.destroy');
    });
});

// Startseite: Angemeldete landen auf der Übersicht, Gäste leitet "auth" zum Login.
Route::get('/', fn () => redirect()->route('dashboard'));
