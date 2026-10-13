<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'title',
        'subject',
        'class_name',
        'teacher_id',
        'exam_date',
        'type',
        'weighting',
        'status',
        'topics',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'weighting' => 'decimal:1',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function averageGrade(): ?float
    {
        $avg = $this->grades()->avg('grade');
        return $avg !== null ? round((float) $avg, 2) : null;
    }
}
