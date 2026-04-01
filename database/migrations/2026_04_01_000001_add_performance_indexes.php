<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── notifications: thêm type index ──────────────────────────
        Schema::table('notifications', function (Blueprint $table) {
            if (!$this->indexExists('notifications', 'notifications_type_index')) {
                $table->index('type', 'notifications_type_index');
            }
            if (!$this->indexExists('notifications', 'notifications_actor_id_index')) {
                $table->index('actor_id', 'notifications_actor_id_index');
            }
        });

        // ── conversations: thêm type+updated_at index ───────────────
        Schema::table('conversations', function (Blueprint $table) {
            if (!$this->indexExists('conversations', 'conversations_type_updated_at_index')) {
                $table->index(['type', 'updated_at'], 'conversations_type_updated_at_index');
            }
        });

        // ── social_groups: thêm privacy+created_at index ───────────
        Schema::table('social_groups', function (Blueprint $table) {
            if (!$this->indexExists('social_groups', 'social_groups_privacy_created_at_index')) {
                $table->index(['privacy', 'created_at'], 'social_groups_privacy_created_at_index');
            }
        });

        // ── messages: thêm message_type index ──────────────────────
        Schema::table('messages', function (Blueprint $table) {
            if (!$this->indexExists('messages', 'messages_message_type_index')) {
                $table->index('message_type', 'messages_message_type_index');
            }
        });

        // ── reposts: thêm user_id+created_at index ──────────────────
        Schema::table('reposts', function (Blueprint $table) {
            if (!$this->indexExists('reposts', 'reposts_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at'], 'reposts_user_id_created_at_index');
            }
        });

        // ── posts: thêm [user_id,created_at] composite nếu chưa có ─
        Schema::table('posts', function (Blueprint $table) {
            if (!$this->indexExists('posts', 'posts_user_id_created_at_index')) {
                $table->index(['user_id', 'created_at'], 'posts_user_id_created_at_index');
            }
            if (!$this->indexExists('posts', 'posts_group_id_created_at_index')) {
                $table->index(['group_id', 'created_at'], 'posts_group_id_created_at_index');
            }
        });

        // ── users: thêm user_type index ─────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            if (!$this->indexExists('users', 'users_user_type_index')) {
                $table->index('user_type', 'users_user_type_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('notifications', fn($t) => $t->dropIndex('notifications_type_index'));
        Schema::table('notifications', fn($t) => $t->dropIndex('notifications_actor_id_index'));
        Schema::table('conversations', fn($t) => $t->dropIndex('conversations_type_updated_at_index'));
        Schema::table('social_groups', fn($t) => $t->dropIndex('social_groups_privacy_created_at_index'));
        Schema::table('messages', fn($t) => $t->dropIndex('messages_message_type_index'));
        Schema::table('reposts', fn($t) => $t->dropIndex('reposts_user_id_created_at_index'));
        Schema::table('posts', fn($t) => $t->dropIndex('posts_user_id_created_at_index'));
        Schema::table('posts', fn($t) => $t->dropIndex('posts_group_id_created_at_index'));
        Schema::table('users', fn($t) => $t->dropIndex('users_user_type_index'));
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
