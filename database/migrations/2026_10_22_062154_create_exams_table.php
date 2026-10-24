<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();

            // TODO: Spalten für eine Arbeit / Prüfung anlegen
            // 1. title, subject, class_name als Text
            // 2. teacher_id: Fremdschlüssel auf "teachers", optional
            //    Wird die Lehrkraft gelöscht, soll die Arbeit bleiben -> nullOnDelete()
            // 3. exam_date als Datum -> date(...)
            // 4. type (z. B. "Klassenarbeit", "Klausur") als Text mit Standardwert
            // 5. weighting: Gewichtung als Dezimalzahl, z. B. 2.0 -> decimal(..., 3, 1)
            // 6. status: "planned" (geplant) oder "completed" (geschrieben), Standard "planned"
            // 7. topics (Themen) als längerer Text, optional -> text(...)->nullable()

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
