<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->foreignId('creator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('requires_approval')->default(false);
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->boolean('is_muted')->default(false);
            $table->enum('status', ['pending', 'active'])->default('active');
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['creator_id']);
            $table->dropColumn(['creator_id', 'requires_approval']);
        });

        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn(['is_muted', 'status']);
        });
    }
};
