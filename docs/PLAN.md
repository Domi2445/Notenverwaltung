# Arbeitsplan: Datenbank und Logik

Die Oberfläche (Blade-Views, CSS, Icons) ist fertig und liegt in
`resources/views` und `resources/css`. Dieser Plan beschreibt, was für
Datenbank und Logik zu bauen ist, damit die Oberfläche funktioniert.

Jeder Schritt nennt **was** zu tun ist, **welche Daten die View erwartet**
und **woran man erkennt, dass er fertig ist**. Hinweise zu passenden
Laravel-Werkzeugen stehen dabei, die Umsetzung selbst ist Teil der Aufgabe.

| # | Schritt | Zeit (ca.) |
|---|---|---|
| 0 | Projekt einrichten | 0,5 h |
| 1 | Datenbank: Migrationen | 1,5 h |
| 2 | Models und Beziehungen | 1 h |
| 3 | Anmeldung, Registrierung, Abmelden | 1,5 h |
| 4 | Schüler | 1 h |
| 5 | Lehrkräfte | 1 h |
| 6 | Noten | 2 h |
| 7 | Arbeiten und Prüfungen | 1,5 h |
| 8 | Stundenplan | 1,5 h |
| 9 | Übersicht (Dashboard) | 1 h |
| 10 | Testdaten (Seeder) | 0,5 h |
| | **Summe** | **ca. 13 h** |

Wenn die Zeit knapp wird: Schritt 8 (Stundenplan) zuletzt machen. Die
Übersicht funktioniert auch mit leerem Stundenplan.

---

## 0 · Projekt einrichten

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
npm install
npm run build
php artisan serve
```

In `.env` den Schulnamen setzen (`SCHOOL_NAME`), er erscheint oben in der
Kopfzeile.

**Fertig, wenn:** `http://127.0.0.1:8000` lädt und auf `/login` weiterleitet.
Die Login-Seite selbst funktioniert erst nach Schritt 3.

---

## 1 · Datenbank: Migrationen

Werkzeug: `php artisan make:migration`, Spaltentypen im Schema-Builder,
`foreignId()->constrained()`.

| Tabelle | Spalten |
|---|---|
| `students` | id, name, class_name, email (optional), birthday (Datum, optional), notes (Text, optional), timestamps |
| `teachers` | id, name, code (Kürzel, max. 10 Zeichen), email, phone, subjects (Text, kommagetrennt), notes (alles außer name/code optional), timestamps |
| `exams` | id, title, subject, class_name, teacher_id (optional, Fremdschlüssel), exam_date (Datum), type, weighting (Dezimalzahl, z. B. 2.0), status (`planned` oder `completed`), topics (optional), timestamps |
| `grades` | id, student_id (optional, Fremdschlüssel), exam_id (optional, Fremdschlüssel), student_name, class_name, subject, grade (Dezimalzahl mit einer Nachkommastelle), assessment (optional), comment (optional), timestamps |
| `timetable_entries` | id, class_name, subject, teacher_id (optional), day_of_week (1–5), period (1–8), time_start, time_end, room (optional), color, timestamps |

Überlegen: Was soll passieren, wenn ein Schüler oder eine Lehrkraft
gelöscht wird, die noch Noten bzw. Stunden hat? (`cascadeOnDelete`,
`nullOnDelete`, …)

**Fertig, wenn:** `php artisan migrate` ohne Fehler läuft.

---

## 2 · Models und Beziehungen

Werkzeug: `php artisan make:model`, `$fillable`, `casts()`, `hasMany`,
`belongsTo`.

- `Student`, `Teacher`, `Exam`, `Grade`, `TimetableEntry`
- Beziehungen, die die Views benutzen:
  - `Student::grades()`, `Grade::student()`, `Grade::exam()`
  - `Exam::teacher()`, `Exam::grades()`
  - `Teacher::exams()`, `Teacher::timetableEntries()`
  - `TimetableEntry::teacher()`
- `birthday` und `exam_date` als Datum casten (die Views rufen `->format()` auf)
- Methoden für Durchschnitte, die die Views aufrufen:
  - `Student::averageGrade()`: Durchschnitt aller Noten, `null` wenn keine
  - `Exam::averageGrade()`: Durchschnitt der Noten dieser Arbeit

**Fertig, wenn:** in `php artisan tinker` z. B. `Student::create([...])` und
`$student->grades` funktionieren.

---

## 3 · Anmeldung, Registrierung, Abmelden

Werkzeug: `Auth::attempt()`, `Auth::login()`, `Auth::logout()`,
`$request->validate()`, `$request->session()->regenerate()`,
Middleware `auth` und `guest`.

| Route-Name | Methode | URL | Zweck |
|---|---|---|---|
| `login` | GET | `/login` | View `auth.login` |
| `login.store` | POST | `/login` | Felder `email`, `password`, `remember` |
| `register` | GET | `/registrieren` | View `auth.register` |
| `register.store` | POST | `/registrieren` | Felder `name`, `email`, `password`, `password_confirmation` |
| `logout` | POST | `/logout` | Abmelden, zurück zum Login |

Die Views zeigen Fehler über `$errors` an (das macht `validate()`
automatisch). Nach erfolgreichem Login oder Registrierung auf `dashboard`
weiterleiten. Alle Routen ab Schritt 4 nur für angemeldete Benutzer.

**Fertig, wenn:** Registrieren, Abmelden und wieder Anmelden klappt und
falsche Passwörter eine Fehlermeldung zeigen.

---

## 4 · Schüler

| Route-Name | Methode | URL |
|---|---|---|
| `students.index` | GET | `/schueler` |
| `students.store` | POST | `/schueler` |
| `students.destroy` | DELETE | `/schueler/{student}` |

**View `students.index` erwartet:**
- `$students`: Liste der Schüler (mit `grades`)
- `$classes`: Liste aller Klassennamen (für die Filter-Buttons)
- `$classFilter`: gewählte Klasse aus `?class=…` oder `null`
- `$search`: Suchtext aus `?search=…` oder `null` (Name oder E-Mail)

**Formularfelder:** `name`, `class_name`, `email`, `birthday`, `notes`.

Nach dem Speichern bzw. Löschen zurückleiten und eine Meldung in die Session
legen: `->with('status', 'Text')`. Das Layout zeigt sie automatisch an.

**Fertig, wenn:** Schüler anlegen, filtern, suchen und löschen funktioniert
und falsche Eingaben (z. B. leerer Name) abgelehnt werden.

---

## 5 · Lehrkräfte

| Route-Name | Methode | URL |
|---|---|---|
| `teachers.index` | GET | `/lehrer` |
| `teachers.store` | POST | `/lehrer` |
| `teachers.destroy` | DELETE | `/lehrer/{teacher}` |

**View `teachers.index` erwartet:**
- `$teachers` mit den Zählern `timetable_entries_count` und `exams_count`
  (Tipp: `withCount()`)
- `$search`: Suche in Name, Kürzel oder Fächern

**Formularfelder:** `name`, `code`, `subjects`, `email`, `phone`, `notes`.

**Fertig, wenn:** Anlegen, Suchen und Löschen funktioniert und ein Kürzel
nicht doppelt vergeben werden kann.

---

## 6 · Noten

| Route-Name | Methode | URL |
|---|---|---|
| `grades.index` | GET | `/noten` |
| `grades.store` | POST | `/noten` |
| `grades.destroy` | DELETE | `/noten/{grade}` |

**View `grades.index` erwartet:**
- `$grades`: gefilterte Noten, neueste zuerst
- Filter aus der URL: `$classFilter` (`?class`), `$subjectFilter` (`?subject`), `$search` (`?search`, Schülername)
- `$totalCount`, `$averageGrade` (auf 2 Stellen gerundet oder `null`)
- `$distribution`: Array `[1 => Anzahl, …, 6 => Anzahl]` für den
  Notenspiegel. Eine Note zählt zur Stufe, zu der sie gerundet gehört
  (1,0–1,5 → 1; 1,6–2,5 → 2; …).
- `$classes`, `$subjects`: alle vorhandenen Klassen bzw. Fächer
- `$students`, `$exams`: für die Auswahllisten im Formular

**Formularfelder:** `student_id` (optional), `student_name`, `class_name`,
`subject`, `grade` (1,0–6,0), `exam_id` (optional), `assessment`, `comment`.

Logik: Wird ein Schüler aus der Liste gewählt, Name und Klasse vom Schüler
übernehmen. Ohne Auswahl ist `student_name` Pflicht.

**Fertig, wenn:** Noten gespeichert, gefiltert und gelöscht werden, der
Notenspiegel stimmt und Noten außerhalb 1–6 abgelehnt werden.

---

## 7 · Arbeiten und Prüfungen

| Route-Name | Methode | URL |
|---|---|---|
| `exams.index` | GET | `/arbeiten` |
| `exams.store` | POST | `/arbeiten` |
| `exams.status` | POST | `/arbeiten/{exam}/status` |
| `exams.destroy` | DELETE | `/arbeiten/{exam}` |

**View `exams.index` erwartet:**
- `$exams` (mit `teacher` und `grades`), Filter `$classFilter` (`?class`) und `$statusFilter` (`?status`)
- `$upcomingCount` (geplant, Datum ab heute), `$plannedCount`, `$completedCount`
- `$classes`, `$teachers`

**Formularfelder:** `title`, `subject`, `class_name`, `exam_date`, `type`,
`weighting`, `teacher_id`, `status`, `topics`.

`exams.status` schaltet zwischen `planned` und `completed` um.

**Fertig, wenn:** Arbeiten anlegen, filtern, Status umschalten und löschen
funktioniert.

---

## 8 · Stundenplan

| Route-Name | Methode | URL |
|---|---|---|
| `timetable.index` | GET | `/stundenplan` |
| `timetable.store` | POST | `/stundenplan` |
| `timetable.destroy` | DELETE | `/stundenplan/{entry}` |

**View `timetable.index` erwartet:**
- `$days`: `[1 => 'Montag', …, 5 => 'Freitag']`
- `$periods`: `[1 => ['start' => '08:00', 'end' => '08:45'], …]` für 8 Stunden
- `$grid`: `$grid[stunde][tag]` ist der Eintrag oder `null`
- `$classes`, `$selectedClass` (`?class`, sonst die erste Klasse)
- `$teachers`, `$teacherFilter` (`?teacher_id`: zeigt statt einer Klasse den Plan dieser Lehrkraft)

**Formularfelder:** `class_name`, `subject`, `day_of_week`, `period`,
`room`, `teacher_id`, `color` (`mint`, `sky`, `purple`, `coral`, `yellow`).
`time_start`/`time_end` aus `$periods` übernehmen.

Überlegen: Was passiert, wenn eine Klasse zur selben Stunde schon Unterricht
hat?

**Fertig, wenn:** Stunden eingetragen und gelöscht werden und der Filter
nach Klasse und Lehrkraft funktioniert.

---

## 9 · Übersicht (Dashboard)

| Route-Name | Methode | URL |
|---|---|---|
| `dashboard` | GET | `/` (nach dem Login) |

**View `dashboard` erwartet:**
- `$studentCount`, `$teacherCount`, `$gradesCount`, `$averageGrade`
- `$classes`: Klassennamen der Schüler (für „in X Klassen“)
- `$upcomingExams`: die nächsten 5 Arbeiten ab heute
- `$recentGrades`: die 6 neuesten Noten
- `$todaySchedule`: Stunden von heute, gruppiert nach `class_name`
  (am Wochenende Montag)
- `$dayName`: Name des angezeigten Tages, z. B. „Dienstag“

**Fertig, wenn:** alle Kacheln echte Zahlen zeigen.

---

## 10 · Testdaten

Werkzeug: `database/seeders/DatabaseSeeder.php`, `php artisan migrate:fresh --seed`.

Ein Benutzerkonto zum Anmelden, ein paar Lehrkräfte, Schüler in 2–3
Klassen, einige Noten, Arbeiten und Stunden.

**Fertig, wenn:** nach `migrate:fresh --seed` alle Seiten mit Inhalt gefüllt sind.

---

## Optional: Tests

Für Schritt 3 bis 7 je einen Feature-Test in `tests/Feature` schreiben
(`php artisan make:test`), z. B. „Gäste werden zum Login umgeleitet“ oder
„Note außerhalb 1–6 wird abgelehnt“. Ausführen mit `php artisan test`.
