<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // TODO: $fillable – welche Spalten dürfen per create() / update() befüllt werden?
    // Alle Spalten aus der Migration außer id und timestamps.
    // -> protected $fillable = [ ... ];

    // TODO: casts() – birthday soll als Datum (Carbon) zurückkommen,
    // weil die Oberfläche $student->birthday->format('d.m.Y') aufruft.
    // -> protected function casts(): array { return [ 'birthday' => '...' ]; }

    // TODO: Beziehung grades() – ein Schüler hat viele Noten
    // -> hasMany(Grade::class)

    // TODO: averageGrade() – Durchschnitt aller Noten des Schülers
    // 1. Durchschnitt über die Beziehung holen -> $this->grades()->avg('grade')
    // 2. Gibt es keine Noten, kommt null zurück – dann null zurückgeben
    // 3. Sonst auf 2 Nachkommastellen runden -> round(...)
    // Rückgabetyp: ?float
}
