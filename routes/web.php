<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Welche Routen die Oberfläche braucht (Name, Methode, URL), steht in
| docs/PLAN.md unter den einzelnen Schritten.
|
*/

// TODO: Gäste-Routen (nur für nicht angemeldete Benutzer)
// -> Route::middleware('guest')->group(function () { ... });
// Darin für den AuthController:
//   GET  /login         -> showLogin     ->name('login')
//   POST /login         -> login         ->name('login.store')
//   GET  /registrieren  -> showRegister  ->name('register')
//   POST /registrieren  -> register      ->name('register.store')
// Schreibweise: Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
// Oben dann "use App\Http\Controllers\AuthController;" ergänzen.

// TODO: Geschützte Routen (nur für angemeldete Benutzer)
// -> Route::middleware('auth')->group(function () { ... });
// Darin:
//   POST   /logout                 -> AuthController@logout          ->name('logout')
//   GET    /                       -> DashboardController@index      ->name('dashboard')
//   GET    /schueler               -> StudentController@index        ->name('students.index')
//   POST   /schueler               -> StudentController@store        ->name('students.store')
//   DELETE /schueler/{student}     -> StudentController@destroy      ->name('students.destroy')
//   … das gleiche Muster für /lehrer (teachers), /noten (grades) und /stundenplan (timetable; Parameter {entry})
//   Für Arbeiten zusätzlich:
//   POST   /arbeiten/{exam}/status -> ExamController@updateStatus    ->name('exams.status')
//
// Wichtig: Der Name in {klammern} muss genau so heißen wie der Parameter im Controller
// (z. B. {student} und destroy(Student $student)), sonst lädt Laravel das Model nicht automatisch.
//
// Sobald die Route "dashboard" auf "/" zeigt, kann die Weiterleitung unten weg.

Route::get('/', function () {
    return redirect('/login');
});
