<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();

            // TODO: Spalten für eine Note anlegen
            // 1. student_id: Fremdschlüssel auf "students", darf leer sein
            //    -> foreignId(...)->nullable()->constrained()
            //    Überlege: Was soll mit den Noten passieren, wenn der Schüler gelöscht wird?
            //    (cascadeOnDelete = mitlöschen, nullOnDelete = Feld leeren)
            // 2. exam_id: Fremdschlüssel auf "exams", darf leer sein (Note ohne Arbeit, z. B. mündlich)
            // 3. student_name und class_name als Text (string)
            //    Warum doppelt zum student_id? Die Oberfläche erlaubt auch Noten per Freitext ohne Schüler-Datensatz.
            // 4. subject (Fach) als Text
            // 5. grade: die Note als Dezimalzahl mit einer Nachkommastelle, z. B. 2.3
            //    -> decimal('grade', 2, 1)  (2 Stellen insgesamt, davon 1 nach dem Komma)
            // 6. assessment (z. B. "Klassenarbeit 1") und comment: beide optional -> nullable()

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
