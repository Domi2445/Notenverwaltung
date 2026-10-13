<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plugin_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('developer_name');
            $table->string('developer_email');
            $table->string('package_name');
            $table->string('version');
            $table->text('description');
            $table->enum('plan_type', ['one_time', 'subscription']);
            $table->unsignedInteger('price_cents');
            $table->string('currency', 3)->default('EUR');
            $table->enum('status', ['submitted', 'review', 'approved', 'rejected'])->default('submitted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plugin_submissions');
    }
};
