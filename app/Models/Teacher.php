<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    // TODO: $fillable – name, code, email, phone, subjects, notes
    protected $fillable = ['name', 'code', 'email', 'phone', 'subjects', 'notes'];

    // TODO: Beziehung exams() – eine Lehrkraft hat viele Arbeiten -> hasMany(Exam::class)
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    // TODO: Beziehung timetableEntries() – eine Lehrkraft hat viele Stunden
    // -> hasMany(TimetableEntry::class)
    // Wichtig: Die Methodennamen müssen genau so heißen, sonst funktioniert
    // withCount(['exams', 'timetableEntries']) im Controller nicht.
    public function timetableEntries()
    {
        return $this->hasMany(TimetableEntry::class);
    }
}
