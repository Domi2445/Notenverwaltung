<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('student_name');
            $table->string('class_name');
            $table->string('subject');
            $table->decimal('grade', 3, 1);
            $table->string('assessment')->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->index(['organization_id', 'class_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
