<?php

namespace App\Modules\Grades;

use App\Plugins\Plugin;

final class GradesPlugin implements Plugin
{
    public function id(): string
    {
        return 'nexora.grades';
    }

    public function name(): string
    {
        return 'Notenverwaltung';
    }

    public function version(): string
    {
        return '0.1.0';
    }

    public function description(): string
    {
        return 'Klassen, Fächer und Leistungen übersichtlich im Griff.';
    }

    public function icon(): string
    {
        return '✦';
    }

    public function accent(): string
    {
        return 'primary';
    }

    public function url(): string
    {
        return route('grades.index');
    }
}
