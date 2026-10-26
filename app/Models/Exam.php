<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Exam extends Model
{
    // TODO: $fillable – alle Spalten aus der Migration außer id und timestamps
    protected $fillable = ['title', 'subject', 'class_name', 'teacher_id', 'exam_date', 'type', 'weighting', 'status', 'topics'];

    // TODO: casts() – exam_date als 'date', weil die Oberfläche
    // $exam->exam_date->format('d') aufruft
    protected $casts = [
        'exam_date' => 'date',
    ];

    // TODO: Beziehung teacher() – eine Arbeit gehört zu einer Lehrkraft -> belongsTo(Teacher::class)

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // TODO: Beziehung grades() – zu einer Arbeit gibt es viele Noten -> hasMany(Grade::class)



    // TODO: averageGrade() – wie bei Student: Durchschnitt der Noten dieser Arbeit,
    // null wenn keine Noten, sonst auf 2 Stellen gerundet

    public function averageGrade()
    {
        $grades = $this->grades()->get();
        if ($grades->isEmpty()) {
            return null;
        }
        return round($grades->avg('grade'), 2);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
