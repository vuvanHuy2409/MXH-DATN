<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('student_id', 20)->nullable()->unique()->after('username');
            $table->string('full_name', 100)->nullable()->after('student_id');
            $table->date('dob')->nullable()->after('full_name');
            $table->string('class', 50)->nullable()->after('dob');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['student_id', 'full_name', 'dob', 'class']);
        });
    }
};
