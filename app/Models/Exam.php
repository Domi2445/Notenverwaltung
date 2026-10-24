<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    // TODO: $fillable – alle Spalten aus der Migration außer id und timestamps

    // TODO: casts() – exam_date als 'date', weil die Oberfläche
    // $exam->exam_date->format('d') aufruft

    // TODO: Beziehung teacher() – eine Arbeit gehört zu einer Lehrkraft -> belongsTo(Teacher::class)

    // TODO: Beziehung grades() – zu einer Arbeit gibt es viele Noten -> hasMany(Grade::class)

    // TODO: averageGrade() – wie bei Student: Durchschnitt der Noten dieser Arbeit,
    // null wenn keine Noten, sonst auf 2 Stellen gerundet
}
