<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    // TODO: $fillable – student_id, exam_id, student_name, class_name, subject,
    // grade, assessment, comment

    // TODO: casts() – grade als Dezimalzahl mit einer Stelle -> 'decimal:1'

    // TODO: Beziehung student() – eine Note gehört zu einem Schüler -> belongsTo(...)

    // TODO: Beziehung exam() – eine Note kann zu einer Arbeit gehören -> belongsTo(...)
}
