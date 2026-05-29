<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Sử dụng DB statement để thay đổi enum vì SQLite/MySQL có cách xử lý khác nhau
        // Ở đây giả định là MySQL dựa trên docker-compose
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'banned', 'suspended', 'flagged') DEFAULT 'active'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN status ENUM('active', 'banned', 'suspended') DEFAULT 'active'");
    }
};
