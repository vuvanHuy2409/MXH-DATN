<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $member) {
            $member->id();
            $member->foreignId('user_id')->constrained()->onDelete('cascade');
            $member->foreignId('post_id')->constrained()->onDelete('cascade');
            $member->unsignedBigInteger('parent_id')->nullable(); // Để trả lời bình luận
            $member->text('content');
            $member->integer('reply_count')->default(0); // Số lượng phản hồi của bình luận này
            $member->timestamp('created_at')->useCurrent();
            
            // Chỉ mục để tăng tốc độ truy vấn
            $member->index('post_id');
            $member->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
