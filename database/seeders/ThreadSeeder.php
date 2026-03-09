<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ThreadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo 3 người dùng mẫu
        $user1 = User::create([
            'username' => 'elonmusk',
            'email' => 'elon@x.com',
            'password_hash' => Hash::make('password'),
            'bio' => 'SpaceX, Tesla, Boring Co, Neuralink. Just vibing.',
            'avatar_url' => 'https://i.pravatar.cc/150?u=elonmusk',
            'follower_count' => 1000,
            'following_count' => 50,
        ]);

        $user2 = User::create([
            'username' => 'zuck',
            'email' => 'mark@meta.com',
            'password_hash' => Hash::make('password'),
            'bio' => 'Building the future of social connection.',
            'avatar_url' => 'https://i.pravatar.cc/150?u=zuck',
            'follower_count' => 2000,
            'following_count' => 100,
        ]);

        $user3 = User::create([
            'username' => 'guest_user',
            'email' => 'guest@example.com',
            'password_hash' => Hash::make('password'),
            'bio' => 'I am a guest exploring this Threads clone.',
            'avatar_url' => 'https://i.pravatar.cc/150?u=guest',
        ]);

        // 2. Tạo các bài viết (Threads) mẫu
        Post::create([
            'user_id' => $user1->id,
            'content' => 'First post on my new Threads clone! Hello world 🚀',
        ]);

        Post::create([
            'user_id' => $user2->id,
            'content' => 'Threads is growing fast. Loving the minimalist vibe here.',
        ]);

        $post3 = Post::create([
            'user_id' => $user1->id,
            'content' => 'Should I buy this platform too? 🧐',
        ]);

        // 3. Tạo reply mẫu cho bài viết số 3
        Post::create([
            'user_id' => $user2->id,
            'parent_id' => $post3->id,
            'content' => 'Focus on Mars first, Elon! 😂',
        ]);
        
        $post3->increment('reply_count');
    }
}
