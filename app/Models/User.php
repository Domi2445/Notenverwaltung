<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'teacher_id',
        'student_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Ein Login kann zu einer Lehrkraft gehören …
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // … oder zu einem Schüler
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Gibt es schon einen Admin? Solange nicht, ist das Registrieren offen.
    public static function adminExists(): bool
    {
        return static::where('role', 'admin')->exists();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    // Admin und Lehrkräfte dürfen Noten und Arbeiten bearbeiten
    public function canEditGrades(): bool
    {
        return $this->isAdmin() || $this->isTeacher();
    }

    // Anzeigename der Rolle, z. B. für die Sidebar
    public function roleLabel(): ?string
    {
        return match ($this->role) {
            'admin' => 'Administration',
            'teacher' => 'Lehrkraft',
            'student' => 'Schüler/in',
            default => null,
        };
    }

    // Klasse des Schülers, zu dem dieser Login gehört (null, wenn keiner verknüpft ist)
    public function studentClass(): ?string
    {
        return $this->student?->class_name;
    }

    // Klassen, die diese Lehrkraft laut Stundenplan unterrichtet
    public function teacherClasses()
    {
        // Ohne verknüpfte Lehrkraft keine Klassen (sonst würde where(..., null) nach IS NULL suchen)
        if (! $this->teacher_id) {
            return collect();
        }

        return TimetableEntry::where('teacher_id', $this->teacher_id)
            ->pluck('class_name')
            ->unique()
            ->values();
    }

    // Klassen, deren Daten dieser Benutzer sehen und bearbeiten darf.
    // null bedeutet: alle Klassen (Admin)
    public function visibleClasses()
    {
        return match ($this->role) {
            'admin' => null,
            'teacher' => $this->teacherClasses(),
            default => collect([$this->studentClass()])->filter()->values(),
        };
    }

    // Darf der Benutzer Daten dieser Klasse sehen bzw. bearbeiten?
    public function canAccessClass(?string $className): bool
    {
        $classes = $this->visibleClasses();

        return $classes === null || $classes->contains($className);
    }
}
