<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = [
        'organization_id',
        'student_id',
        'exam_id',
        'student_name',
        'class_name',
        'subject',
        'grade',
        'assessment',
        'comment',
    ];

    protected function casts(): array
    {
        return ['grade' => 'decimal:1'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
