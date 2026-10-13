<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimetableEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'day_of_week',
        'period',
        'time_start',
        'time_end',
        'subject',
        'class_name',
        'room',
        'teacher_id',
        'color',
    ];

    public static array $days = [
        1 => 'Montag',
        2 => 'Dienstag',
        3 => 'Mittwoch',
        4 => 'Donnerstag',
        5 => 'Freitag',
    ];

    public static array $defaultPeriods = [
        1 => ['start' => '08:00', 'end' => '08:45'],
        2 => ['start' => '08:50', 'end' => '09:35'],
        3 => ['start' => '09:55', 'end' => '10:40'],
        4 => ['start' => '10:45', 'end' => '11:30'],
        5 => ['start' => '11:45', 'end' => '12:30'],
        6 => ['start' => '12:35', 'end' => '13:20'],
        7 => ['start' => '13:30', 'end' => '14:15'],
        8 => ['start' => '14:20', 'end' => '15:05'],
    ];

    public function getDayNameAttribute(): string
    {
        return self::$days[$this->day_of_week] ?? 'Unbekannt';
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
