<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 1 = Montag ... 5 = Freitag
            $table->unsignedTinyInteger('period'); // 1 .. 8
            $table->string('time_start', 10)->default('08:00');
            $table->string('time_end', 10)->default('08:45');
            $table->string('subject');
            $table->string('class_name');
            $table->string('room', 30)->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->string('color', 20)->default('mint'); // coral, mint, yellow, sky, purple
            $table->timestamps();

            $table->index(['organization_id', 'class_name', 'day_of_week']);
            $table->index(['organization_id', 'teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
    }
};
