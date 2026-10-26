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
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();

            // TODO: Spalten für eine Unterrichtsstunde anlegen
            // 1. class_name und subject als Text
            // 2. teacher_id: Fremdschlüssel auf "teachers", optional
            //    Wenn die Lehrkraft gelöscht wird, soll die Stunde bleiben -> nullOnDelete()
            // 3. day_of_week: Wochentag als kleine Zahl (1 = Montag … 5 = Freitag)
            //    -> unsignedTinyInteger(...)
            // 4. period: die wievielte Stunde (1 … 8), ebenfalls unsignedTinyInteger
            // 5. time_start und time_end als kurzer Text, z. B. "08:00" -> string(..., 5)
            // 6. room (Raum) optional, color mit Standardwert "mint" -> default('mint')
            // 7. Bonus: Eine Klasse darf zur selben Zeit nur eine Stunde haben.
            //    -> $table->unique([... drei Spalten ...]);

            $table->timestamps();
            $table->string('class_name');
            $table->string('subject');
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->unsignedTinyInteger('period');
            $table->string('time_start', 5);
            $table->string('time_end', 5);
            $table->string('room')->nullable();
            $table->string('color')->default('mint');
            $table->unique(['class_name', 'day_of_week', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
    }
};
