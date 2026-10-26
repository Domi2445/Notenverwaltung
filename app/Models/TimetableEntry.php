<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableEntry extends Model
{
    // TODO: $fillable – alle Spalten aus der Migration außer id und timestamps
    protected $fillable = ['class_name', 'subject', 'teacher_id', 'day_of_week', 'period', 'time_start', 'time_end', 'room', 'color'];

    // TODO: Konstante DAYS mit den Wochentagen, damit sie nur an einer Stelle stehen:
    // [1 => 'Montag', 2 => 'Dienstag', …, 5 => 'Freitag']
    // -> public const DAYS = [ ... ];

    public const DAYS = [
        1 => 'Montag',
        2 => 'Dienstag',
        3 => 'Mittwoch',
        4 => 'Donnerstag',
        5 => 'Freitag'
    ];

    // TODO: Konstante PERIODS mit Start- und Endzeit jeder Stunde (1 bis 9, Berufsschule):
    // [1 => ['start' => '08:00', 'end' => '08:45'], 2 => ['start' => '08:45', 'end' => '09:30'], …]
    // Die Oberfläche braucht genau die Schlüssel 'start' und 'end'.
    public const PERIODS = [
        1 => ['start' => '08:00', 'end' => '08:45'],
        2 => ['start' => '08:45', 'end' => '09:30'],
        3 => ['start' => '09:30', 'end' => '10:15'],
        4 => ['start' => '10:30', 'end' => '11:15'],
        5 => ['start' => '11:15', 'end' => '12:00'],
        6 => ['start' => '12:00', 'end' => '12:45'],
        7 => ['start' => '13:45', 'end' => '14:30'],
        8 => ['start' => '14:30', 'end' => '15:15'],
        9 => ['start' => '15:15', 'end' => '16:00']
    ];

    // TODO: Beziehung teacher() -> belongsTo(Teacher::class)

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
