<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('conversation_id')->nullable()->constrained('conversations')->onDelete('cascade');
            $table->enum('post_type', ['social', 'assignment', 'announcement'])->default('social')->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['conversation_id']);
            $table->dropColumn(['conversation_id', 'post_type']);
        });
    }
};
