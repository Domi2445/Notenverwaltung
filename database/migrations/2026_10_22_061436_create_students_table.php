<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('class_name');
            $table->string('email');
            $table->date('date_of_birth');
            $table->string('phone_number');
            $table->string('address');

            // TODO: an die Oberfläche anpassen
            // 1. date_of_birth heißt in der Oberfläche "birthday" -> Spalte umbenennen
            // 2. email, birthday, phone_number und address sind im Formular freiwillig
            //    -> jeweils ->nullable() anhängen, sonst gibt es beim Speichern einen Fehler
            // 3. Die Oberfläche hat ein Feld "Notizen / Förderbedarf"
            //    -> Spalte notes als text, optional

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
