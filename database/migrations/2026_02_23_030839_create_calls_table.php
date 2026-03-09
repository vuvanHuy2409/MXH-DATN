<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
            $table->foreignId('caller_id')->constrained('users')->onDelete('cascade');
            $table->enum('call_type', ['audio', 'video']);
            $table->enum('status', ['ongoing', 'completed', 'missed', 'declined'])->default('ongoing');
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            
            $table->index(['caller_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
