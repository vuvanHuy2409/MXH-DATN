<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->boolean('reminded')->default(false);
            $table->timestamp('reminded_at')->nullable();
            $table->timestamps();

            $table->unique(['appointment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_participants');
    }
};
