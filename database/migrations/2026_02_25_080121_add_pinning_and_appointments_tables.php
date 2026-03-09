<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Thêm cột ghim vào bảng messages
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_pinned')->default(false)->after('is_read');
        });

        // Tạo bảng lịch hẹn
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->onDelete('cascade');
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->dateTime('appointment_time');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('is_pinned');
        });
        Schema::dropIfExists('appointments');
    }
};
