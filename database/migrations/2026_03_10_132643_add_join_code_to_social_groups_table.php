<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_groups', function (Blueprint $table) {
            $table->string('join_code', 5)->unique()->nullable()->after('privacy');
        });
    }

    public function down(): void
    {
        Schema::table('social_groups', function (Blueprint $table) {
            $table->dropColumn('join_code');
        });
    }
};
