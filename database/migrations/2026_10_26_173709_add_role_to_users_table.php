<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // admin, teacher oder student
            $table->string('role')->default('student');
            // Ein Login gehört zu einer Lehrkraft oder zu einem Schüler (Admin: beides leer)
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('teacher_id');
            $table->dropConstrainedForeignId('student_id');
            $table->dropColumn('role');
        });
    }
};
