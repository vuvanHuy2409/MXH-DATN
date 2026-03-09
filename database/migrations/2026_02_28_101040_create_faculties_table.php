<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Insert some default faculties like in your mxh.sql
        DB::table('faculties')->insert([
            ['name' => 'Công nghệ Thông tin'],
            ['name' => 'Kỹ thuật Phần mềm'],
            ['name' => 'An toàn Thông tin'],
            ['name' => 'Hệ thống Thông tin'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
