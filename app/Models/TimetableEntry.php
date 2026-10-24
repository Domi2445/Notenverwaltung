<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableEntry extends Model
{
    // TODO: $fillable – alle Spalten aus der Migration außer id und timestamps

    // TODO: Konstante DAYS mit den Wochentagen, damit sie nur an einer Stelle stehen:
    // [1 => 'Montag', 2 => 'Dienstag', …, 5 => 'Freitag']
    // -> public const DAYS = [ ... ];

    // TODO: Konstante PERIODS mit Start- und Endzeit jeder Stunde (1 bis 8):
    // [1 => ['start' => '08:00', 'end' => '08:45'], 2 => ['start' => '08:50', ...], …]
    // Die Oberfläche braucht genau die Schlüssel 'start' und 'end'.

    // TODO: Beziehung teacher() -> belongsTo(Teacher::class)
}
