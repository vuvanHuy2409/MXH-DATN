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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('posts')->onDelete('cascade');
            $table->text('content');
            $table->string('link_url', 512)->nullable();
            $table->unsignedInteger('like_count')->default(0);
            $table->unsignedInteger('reply_count')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index(['parent_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
