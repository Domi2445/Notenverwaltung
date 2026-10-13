<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('subject');
            $table->string('class_name');
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->date('exam_date');
            $table->string('type')->default('Klassenarbeit');
            $table->decimal('weighting', 3, 1)->default(1.0);
            $table->string('status')->default('planned'); // planned, completed, graded
            $table->text('topics')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'class_name', 'exam_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
