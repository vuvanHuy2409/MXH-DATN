<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('social_groups', function (Blueprint $table) {
            $table->enum('type', ['community', 'class'])->default('community')->after('id');
            $table->string('class_name')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('social_groups', function (Blueprint $table) {
            $table->dropColumn(['type', 'class_name']);
        });
    }
};
