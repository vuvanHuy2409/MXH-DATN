<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Follow;
use App\Models\TeacherDetail;
use App\Models\StudentDetail;
use App\Models\Faculty;
use App\Models\Conversation;
use App\Models\Participant;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class LargeSampleSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');
        $faculties = Faculty::all();
        
        if ($faculties->isEmpty()) {
            $this->command->error('Vui lòng tạo dữ liệu bảng Khoa trước!');
            return;
        }

        // Xóa dữ liệu cũ để làm mới (trừ bảng faculties)
        $this->command->info('Đang dọn dẹp dữ liệu cũ...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Post::truncate();
        Comment::truncate();
        DB::table('follows')->truncate();
        TeacherDetail::truncate();
        StudentDetail::truncate();
        Conversation::truncate();
        Participant::truncate();
        Message::truncate();
        Like::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $facultyIds = $faculties->pluck('id')->toArray();
        $password = Hash::make('password123');

        // Danh sách nội dung tiếng Việt mẫu
        $vietnameseBios = [
            'Sống là để cống hiến.', 'Yêu công nghệ, thích khám phá.', 'Sinh viên năm cuối đang nỗ lực.',
            'Giảng viên tâm huyết với nghề.', 'Học, học nữa, học mãi.', 'Đam mê lập trình và trà sữa.',
            'Tìm kiếm cơ hội mới.', 'Kết nối bạn bè khắp mọi nơi.', 'Yêu thích nghiên cứu khoa học.',
            'Thích đi du lịch và chụp ảnh.', 'Thế giới này thật rộng lớn.', 'Luôn giữ tinh thần lạc quan.'
        ];

        $vietnamesePostContents = [
            'Hôm nay thời tiết thật đẹp, mọi người thấy sao?',
            'Có ai biết tài liệu về Laravel hay không, chia sẻ cho mình với!',
            'Vừa hoàn thành xong đồ án tốt nghiệp, mệt nhưng vui.',
            'Chào buổi sáng cả nhà, chúc mọi người một ngày làm việc hiệu quả.',
            'Đang tìm cộng sự cho dự án khởi nghiệp về AI.',
            'Thông báo: Lớp nghỉ học hôm nay do thầy bận việc đột xuất.',
            'Cuộc sống là những chuyến đi dài.',
            'Học lập trình không khó, quan trọng là phải kiên trì.',
            'Review món bún đậu mắm tôm siêu ngon tại cổng trường.',
            'Cần tìm phòng trọ gần trường, ai có thông tin inbox mình nhé!',
            'Mọi người cho mình hỏi về thủ tục đăng ký học phần với.',
            'Tin vui: Khoa mình vừa giành giải nhất cuộc thi nghiên cứu khoa học!',
            'Cuối tuần rồi, đi đâu chơi đây anh em ơi?',
            'Laravel 11 có gì mới không nhỉ? Đang tìm hiểu dần.',
            'Áp lực tạo nên kim cương, cố gắng lên nào!'
        ];

        $vietnameseComments = [
            'Đúng vậy bạn ơi!', 'Bài viết rất hữu ích, cảm ơn bạn.', 'Mình cũng nghĩ thế.',
            'Thật tuyệt vời!', 'Cho mình xin link với ạ.', 'Chúc mừng bạn nhé!',
            'Đỉnh quá thầy ơi.', 'Món này mình cũng thích lắm.', 'Inbox mình nhé.',
            'Haha, hài hước quá.', 'Tuyệt vời ông mặt trời.', 'Hy vọng sớm được gặp mọi người.'
        ];

        $this->command->info('Đang tạo 100 người dùng (Giảng viên & Sinh viên)...');
        
        $users = [];
        
        // 1. Tạo Giảng viên
        for ($i = 0; $i < 20; $i++) {
            $user = User::create([
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'password_hash' => $password,
                'user_type' => 'teacher',
                'bio' => $faker->randomElement($vietnameseBios),
                'avatar_url' => null, // Không cần avatar
                'link_url' => $faker->url,
            ]);
            
            TeacherDetail::create([
                'user_id' => $user->id,
                'full_name' => $faker->name,
                'faculty_id' => $faker->randomElement($facultyIds),
            ]);
            $users[] = $user;
        }

        // 2. Tạo Sinh viên
        for ($i = 0; $i < 80; $i++) {
            $user = User::create([
                'username' => $faker->unique()->userName,
                'email' => $faker->unique()->safeEmail,
                'password_hash' => $password,
                'user_type' => 'student',
                'bio' => $faker->randomElement($vietnameseBios),
                'avatar_url' => null, // Không cần avatar
                'link_url' => $faker->url,
            ]);
            
            StudentDetail::create([
                'user_id' => $user->id,
                'student_id' => 'SV' . $faker->unique()->numberBetween(100000, 999999),
                'full_name' => $faker->name,
                'dob' => $faker->date('Y-m-d', '2005-01-01'),
                'class' => $faker->randomElement(['DCT1211', 'DCT1212', 'DKP1211', 'DQT1211', 'DLK1211']),
                'faculty_id' => $faker->randomElement($facultyIds),
            ]);
            $users[] = $user;
        }

        $userIds = collect($users)->pluck('id')->toArray();

        $this->command->info('Đang tạo 200 bài viết tiếng Việt...');
        $posts = [];
        for ($i = 0; $i < 200; $i++) {
            $posts[] = Post::create([
                'user_id' => $faker->randomElement($userIds),
                'content' => $faker->randomElement($vietnamesePostContents),
                'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
            ]);
        }

        $postIds = collect($posts)->pluck('id')->toArray();

        $this->command->info('Đang tạo 500 bình luận tiếng Việt...');
        for ($i = 0; $i < 500; $i++) {
            Comment::create([
                'post_id' => $faker->randomElement($postIds),
                'user_id' => $faker->randomElement($userIds),
                'content' => $faker->randomElement($vietnameseComments),
                'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
            ]);
        }

        $this->command->info('Đang tạo 1000 lượt thích...');
        for ($i = 0; $i < 1000; $i++) {
            try {
                Like::insertOrIgnore([
                    'post_id' => $faker->randomElement($postIds),
                    'user_id' => $faker->randomElement($userIds),
                    'created_at' => now(),
                ]);
            } catch (\Exception $e) {}
        }

        $this->command->info('Đang tạo 500 lượt theo dõi...');
        for ($i = 0; $i < 500; $i++) {
            $followerId = $faker->randomElement($userIds);
            $followingId = $faker->randomElement($userIds);
            
            if ($followerId !== $followingId) {
                try {
                    DB::table('follows')->insertOrIgnore([
                        'follower_id' => $followerId,
                        'following_id' => $followingId,
                        'created_at' => now(),
                    ]);
                } catch (\Exception $e) {}
            }
        }

        $this->command->info('Dữ liệu mẫu tiếng Việt đã được tạo thành công!');
    }
}
