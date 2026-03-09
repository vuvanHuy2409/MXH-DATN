<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('full_name');
            $table->foreignId('faculty_id')->constrained('faculties')->onDelete('cascade');
        });

        Schema::create('student_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('student_id')->nullable();
            $table->string('full_name');
            $table->date('dob')->nullable();
            $table->string('class')->nullable();
            $table->foreignId('faculty_id')->constrained('faculties')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_details');
        Schema::dropIfExists('teacher_details');
    }
};
