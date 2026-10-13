<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\TeamMember;
use App\Models\Organization;
use App\Models\User;
use App\Models\PluginSubmission;
use App\Models\Subscription;
use App\Models\Grade;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return to_route('demo');
});
Route::view('/demo', 'demo')->name('demo');
Route::get('/demo/start', function (Request $request) {
    $demoUser = User::firstOrCreate(
        ['email' => 'demo@nexora.local'],
        [
            'name' => 'Nexora Demo',
            'password' => str()->random(64),
            'email_verified_at' => now(),
        ],
    );
    $demoOrganization = Organization::firstOrCreate(
        ['slug' => 'nexora-demo'],
        ['name' => 'Nexora Demo Organisation', 'status' => 'active'],
    );

    if (! $demoUser->organizations()->whereKey($demoOrganization->id)->exists()) {
        $demoOrganization->users()->attach($demoUser, ['role' => 'owner']);
    }

    \App\Services\SchoolDemoDataSeeder::seed($demoOrganization);

    Auth::login($demoUser);
    $request->session()->regenerate();

    return to_route('customer.dashboard');
})->name('demo.start');
Route::view('/dokumentation', 'public.documentation')->name('documentation');
Route::view('/impressum', 'public.imprint')->name('imprint');
Route::view('/datenschutz', 'public.privacy')->name('privacy');
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::get('/registrieren', [AuthController::class, 'register'])->name('register');
Route::post('/registrieren', [AuthController::class, 'storeRegistration'])->name('register.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');
Route::middleware(['auth', 'organization.member'])->group(function () {
    // Dashboard & Overview
    Route::get('/kundenbereich', [\App\Http\Controllers\DashboardController::class, 'index'])->name('customer.dashboard');

    // Notenverwaltung
    Route::get('/notenverwaltung', [\App\Http\Controllers\GradeController::class, 'index'])->name('grades.index');
    Route::post('/notenverwaltung/noten', [\App\Http\Controllers\GradeController::class, 'store'])->name('grades.store');
    Route::delete('/notenverwaltung/noten/{grade}', [\App\Http\Controllers\GradeController::class, 'destroy'])->name('grades.destroy');

    // Stundenplan
    Route::get('/stundenplan', [\App\Http\Controllers\TimetableController::class, 'index'])->name('timetable.index');
    Route::post('/stundenplan', [\App\Http\Controllers\TimetableController::class, 'store'])->name('timetable.store');
    Route::delete('/stundenplan/{entry}', [\App\Http\Controllers\TimetableController::class, 'destroy'])->name('timetable.destroy');

    // Arbeiten & Prüfungen
    Route::get('/arbeiten', [\App\Http\Controllers\ExamController::class, 'index'])->name('exams.index');
    Route::post('/arbeiten', [\App\Http\Controllers\ExamController::class, 'store'])->name('exams.store');
    Route::post('/arbeiten/{exam}/status', [\App\Http\Controllers\ExamController::class, 'updateStatus'])->name('exams.status');
    Route::delete('/arbeiten/{exam}', [\App\Http\Controllers\ExamController::class, 'destroy'])->name('exams.destroy');

    // Schüler
    Route::get('/schueler', [\App\Http\Controllers\StudentController::class, 'index'])->name('students.index');
    Route::post('/schueler', [\App\Http\Controllers\StudentController::class, 'store'])->name('students.store');
    Route::delete('/schueler/{student}', [\App\Http\Controllers\StudentController::class, 'destroy'])->name('students.destroy');

    // Lehrer
    Route::get('/lehrer', [\App\Http\Controllers\TeacherController::class, 'index'])->name('teachers.index');
    Route::post('/lehrer', [\App\Http\Controllers\TeacherController::class, 'store'])->name('teachers.store');
    Route::delete('/lehrer/{teacher}', [\App\Http\Controllers\TeacherController::class, 'destroy'])->name('teachers.destroy');
});
Route::view('/support', 'platform.support')->name('support');
Route::view('/einstellungen', 'platform.settings')->name('settings');
Route::get('/team', function () {
    return view('platform.team', [
        'members' => TeamMember::query()->latest()->get(),
    ]);
})->name('team');

Route::post('/team/invitations', function (Request $request) {
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:255', 'unique:team_members,email'],
        'role' => ['required', 'in:administrator,editor,viewer'],
    ]);

    TeamMember::create($validated);

    return to_route('team')->with('status', "Einladung an {$validated['email']} wurde vorgemerkt.");
})->name('team.invite');
Route::view('/module', 'platform.marketplace')->name('marketplace');
Route::view('/entwickler', 'platform.developer')->name('developer');
Route::post('/entwickler/einreichung', function (Request $request) {
    $validated = $request->validate([
        'developer_name' => ['required', 'string', 'max:120'],
        'developer_email' => ['required', 'email', 'max:255'],
        'package_name' => ['required', 'regex:/^[a-z0-9_.-]+\/[a-z0-9_.-]+$/', 'max:160'],
        'version' => ['required', 'string', 'max:30'],
        'description' => ['required', 'string', 'max:2000'],
        'plan_type' => ['required', 'in:one_time,subscription'],
        'price_eur' => ['required', 'numeric', 'min:0', 'max:999999'],
    ]);

    PluginSubmission::create([
        ...$validated,
        'price_cents' => (int) round(((float) $validated['price_eur']) * 100),
        'currency' => 'EUR',
    ]);

    return to_route('developer')->with('status', 'Dein Modul wurde zur technischen Prüfung eingereicht.');
})->name('developer.submit');

Route::middleware(['auth', 'platform.admin'])->prefix('admin')->group(function () {
    Route::get('/kunden', function () {
        return view('admin.customers', [
            'organizations' => Organization::query()
                ->withCount('users')
                ->with(['subscriptions' => fn ($query) => $query->where('status', 'active')])
                ->latest()
                ->get(),
        ]);
    })->name('admin.customers');
    Route::get('/lizenzen', function () {
        return view('admin.licenses', [
            'subscriptions' => Subscription::query()
                ->where('status', 'active')
                ->with('organization')
                ->latest()
                ->get(),
            'submissions' => PluginSubmission::query()->latest()->get(),
        ]);
    })->name('admin.licenses');
});
