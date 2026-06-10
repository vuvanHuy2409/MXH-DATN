<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Mở rộng ENUM type: thêm 'message'
        DB::statement("ALTER TABLE `reports`
            MODIFY COLUMN `type` ENUM('post','user','comment','message') NOT NULL");

        // Mở rộng ENUM status: thêm 'ignored' (đồng bộ với code Laravel dùng 'ignored')
        DB::statement("ALTER TABLE `reports`
            MODIFY COLUMN `status` ENUM('pending','reviewed','resolved','dismissed','ignored') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `reports`
            MODIFY COLUMN `type` ENUM('post','user','comment') NOT NULL");

        DB::statement("ALTER TABLE `reports`
            MODIFY COLUMN `status` ENUM('pending','reviewed','resolved','dismissed') NOT NULL DEFAULT 'pending'");
    }
};
