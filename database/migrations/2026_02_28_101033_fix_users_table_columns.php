<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->enum('user_type', ['student', 'teacher', 'admin'])->default('student')->after('role');
            }
            if (!Schema::hasColumn('users', 'link_url')) {
                $table->string('link_url')->nullable()->after('bio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['user_type', 'link_url']);
        });
    }
};
