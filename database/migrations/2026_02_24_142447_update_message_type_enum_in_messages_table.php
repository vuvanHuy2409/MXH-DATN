<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE messages MODIFY COLUMN message_type ENUM('text', 'image', 'video', 'file', 'sticker', 'link', 'call_log', 'system') DEFAULT 'text'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE messages MODIFY COLUMN message_type ENUM('text', 'image', 'video', 'call_log', 'system') DEFAULT 'text'");
    }
};
