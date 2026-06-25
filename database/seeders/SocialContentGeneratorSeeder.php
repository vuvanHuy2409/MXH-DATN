<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SocialContentGeneratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt Query Log để giải phóng bộ nhớ RAM
        DB::connection()->disableQueryLog();

        $this->command->info('1. Dọn dẹp dữ liệu mạng xã hội cũ...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('posts')->truncate();
        DB::table('post_media')->truncate();
        DB::table('comments')->truncate();
        DB::table('likes')->truncate();
        DB::table('follows')->truncate();
        DB::table('conversations')->truncate();
        DB::table('participants')->truncate();
        DB::table('messages')->truncate();
        DB::table('appointments')->truncate();
        DB::table('reports')->truncate();
        DB::table('reposts')->truncate();
        DB::table('notifications')->truncate();
        DB::table('social_groups')->truncate();
        DB::table('group_members')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('2. Tải danh sách người dùng từ cơ sở dữ liệu...');
        
        // Pluck danh sách ID của Sinh viên và Giảng viên
        $students = DB::table('student_details')->select('user_id', 'class')->get();
        $studentIds = $students->pluck('user_id')->toArray();
        
        $teacherIds = DB::table('teacher_details')->pluck('user_id')->toArray();
        
        $allUserIds = array_merge($studentIds, $teacherIds);
        $userCount = count($allUserIds);

        if ($userCount === 0) {
            $this->command->error('Cơ sở dữ liệu chưa có tài khoản nào. Vui lòng chạy AccountGeneratorSeeder trước!');
            return;
        }

        // Nhóm sinh viên theo lớp học để tự động cho vào nhóm lớp
        $studentsByClass = [];
        foreach ($students as $s) {
            $studentsByClass[$s->class][] = $s->user_id;
        }
        $classes = array_keys($studentsByClass);

        $adminUser = DB::table('users')->where('role', 'admin')->first();
        $adminId = $adminUser ? $adminUser->id : $allUserIds[0];

        $this->command->info('3. Tạo các nhóm cộng đồng và nhóm lớp học...');
        
        $communityNames = [
            // Thể thao & Võ thuật
            'CLB Bóng đá EAUT', 'CLB Bóng rổ EAUT', 'Cộng Đồng Bóng Chuyền Đông Á', 'CLB Cầu Lông EAUT',
            'CLB Bóng Bàn Đông Á', 'CLB Cờ Vua & Cờ Tướng EAUT', 'CLB Karate EAUT', 'CLB Taekwondo EAUT',
            'CLB Vovinam Đông Á', 'Cộng Đồng Gym & Fitness EAUT', 'CLB Chạy Bộ & Marathon', 'CLB Yoga & Sức Khỏe',
            'CLB Bơi Lội EAUT', 'CLB Cầu Mây Đông Á', 'Hội Đạp Xe EAUT', 'CLB Tennis Đông Á',
            // Nghệ thuật & Văn hóa
            'CLB Âm nhạc EAUT', 'CLB Guitar & Nhạc Cụ', 'CLB Piano & Organ EAUT', 'CLB Nhảy Hiện Đại (Dance Club)',
            'CLB Múa Truyền Thống EAUT', 'CLB Nhiếp Ảnh & Media (E-Photo)', 'CLB Hội Họa & Mỹ Thuật',
            'Cộng Đồng Thiết Kế Đồ Họa EAUT', 'CLB Kịch & Điện Ảnh Đông Á', 'CLB Radio & Podcasting EAUT',
            'CLB Kịch Nói EAUT', 'CLB Vẽ Tranh & Ký Họa', 'Cộng Đồng Acoustic EAUT', 'CLB Ảo Thuật E-Connect',
            // Học thuật & Công nghệ
            'Cộng Đồng Đam Mê Lập Trình', 'CLB Robotics & IoT EAUT', 'Diễn Đàn Học Thuật Công Nghệ',
            'CLB Tin Học Văn Phòng', 'CLB AI & Machine Learning EAUT', 'CLB Web & Mobile Development',
            'Cộng Đồng Thiết Kế Vi Mạch', 'CLB Nghiên Cứu Khoa Học Trẻ', 'CLB Khởi Nghiệp Trẻ EAUT',
            'CLB Tranh Biện Đông Á (Debate Club)', 'CLB Thuyết Trình & MC EAUT', 'CLB Kỹ Năng Mềm E-Connect',
            'Diễn Đàn Khoa Học Máy Tính', 'CLB Nghiên Cứu Blockchain EAUT', 'Cộng Đồng Lập Trình Python', 'Hội Thảo Khoa Học Trẻ',
            // Ngoại ngữ & Giao lưu
            'CLB Tiếng Anh E-Connect', 'Cộng Đồng Tiếng Nhật EAUT', 'CLB Tiếng Hàn Đông Á',
            'CLB Tiếng Trung E-Connect', 'Cộng Đồng Ngoại Ngữ & Giao Lưu', 'CLB Du Học & Học Bổng',
            'CLB Tiếng Đức EAUT', 'Hội Luyện Thi IELTS E-Connect', 'CLB Tiếng Nhật Cấp Tốc', 'Cộng Đồng Tiếng Anh Giao Tiếp',
            // Tình nguyện & Xã hội
            'Hội Sinh Viên Tình Nguyện', 'CLB Hiến Máu Nhân Đạo EAUT', 'CLB Bảo Vệ Môi Trường (Green EAUT)',
            'Cộng Đồng Từ Thiện & Nhân Ái', 'CLB Công Tác Xã Hội Đông Á',
            'CLB Áo Xanh Tình Nguyện', 'Hội Chữ Thập Đỏ EAUT', 'Đội Tình Nguyện Tiếp Sức Mùa Thi',
            // Đồng hương
            'Hội Đồng Hương Nghệ An EAUT', 'Hội Đồng Hương Thanh Hóa EAUT', 'Hội Đồng Hương Hà Tĩnh EAUT',
            'Hội Đồng Hương Nam Định EAUT', 'Hội Đồng Hương Thái Bình EAUT', 'Hội Đồng Hương Hải Dương EAUT',
            'Hội Đồng Hương Bắc Ninh EAUT', 'Hội Đồng Hương Quảng Ninh EAUT', 'Hội Đồng Hương Phú Thọ EAUT',
            'Hội Đồng Hương Ninh Bình EAUT', 'Hội Đồng Hương Hưng Yên EAUT', 'Hội Đồng Hương Hà Nội EAUT',
            // Giải trí & Đời sống
            'CLB Sách & Hành Trình', 'Câu Lạc Bộ Boardgame EAUT', 'Cộng Đồng Ma Sói Đông Á',
            'Hội Thể Thao Điện Tử (E-Sports)', 'CLB Liên Quân Mobile EAUT', 'CLB Tốc Chiến & LoL EAUT',
            'CLB Guitar & Acoustic', 'Hội Độc Thân EAUT', 'Góc Tìm Kiếm Trọ & Việc Làm',
            'Hội Thích Đi Phượt EAUT', 'Cộng Đồng Du Lịch & Khám Phá', 'Hội Trao Đổi Đồ Cũ EAUT',
            'Hội Thích Chụp Ảnh Check-in', 'Diễn Đàn Confession EAUT', 'Cộng Đồng Review Đồ Ăn Quanh Trường',
            'CLB Valorant EAUT', 'CLB FIFA Online EAUT', 'Hội Đấu Trường Chân Lý EAUT', 'CLB Boardgame Ma Sói',
            'Hội Nuôi Mèo EAUT', 'Hội Thích Ăn Vặt Cổng Trường', 'CLB Phượt Thủ Đông Á',
            // Chuyên ngành & Nghề nghiệp
            'Hội Yêu Thích Logistics', 'Câu Lạc Bộ Dược Khoa Đông Á', 'Diễn Đàn Luật Học Trẻ',
            'CLB Điện - Điện Tử Đông Á', 'CLB Quản Trị Kinh Doanh EAUT', 'Cộng Đồng Marketing Trẻ',
            'Diễn Đàn Kế Toán - Kiểm Toán', 'Cộng Đồng Cơ Khí - Chế Tạo Máy', 'CLB Kỹ Sư Xây Dựng Đông Á',
            'Cộng Đồng Công Nghệ Thực Phẩm', 'CLB Điều Dưỡng EAUT', 'CLB Du Lịch & Lữ Hành Đông Á',
            'Diễn Đàn Công Nghệ Ô Tô EAUT', 'CLB Thiết Kế Thời Trang', 'Cộng Đồng Thương Mại Điện Tử',
            'Cộng Đồng Cơ Điện Tử EAUT', 'Hội Lập Trình Viên Laravel', 'Cộng Đồng Thiết Kế 3D', 'Cộng Đồng Kinh Doanh Số',
            // Sở thích & Đời sống sinh viên
            'CLB Cây Cảnh & Bonsai EAUT', 'CLB Nuôi Thú Cưng EAUT', 'Cộng Đồng Thần Số Học & Tarot',
            'Hội Thích Đọc Truyện Tranh (Manga/Anime)', 'Cộng Đồng Đam Mê Phim Ảnh', 'Hội Thích Săn Sale & Mua Sắm',
            'CLB Cơm Trưa Sinh Viên', 'CLB Trà Đá Chém Gió EAUT', 'Hội Cựu Sinh Viên EAUT',
            'Góc Chia Sẻ Tài Liệu Học Tập', 'Cộng Đồng Giao Lưu K13', 'Cộng Đồng Giao Lưu K14',
            'Cộng Đồng Giao Lưu K15', 'CLB Phim Ảnh EAUT', 'Góc Chia Sẻ Laptop & Gear', 'Cộng Đồng Gymers Đông Á'
        ];

        $groupsToInsert = [];
        
        // Tạo cộng đồng công cộng
        foreach ($communityNames as $name) {
            $groupsToInsert[] = [
                'type' => 'community',
                'name' => $name,
                'class_name' => null,
                'slug' => Str::slug($name) . '-' . Str::random(5),
                'description' => "Cộng đồng giao lưu, chia sẻ dành cho sinh viên và giảng viên: $name.",
                'avatar_url' => '/uploads/groups/community-default.png',
                'creator_id' => $adminId,
                'privacy' => 'public',
                'join_code' => strtoupper(Str::random(5)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Tạo nhóm lớp riêng tư
        foreach ($classes as $class) {
            $name = "Lớp Học $class";
            $groupsToInsert[] = [
                'type' => 'class',
                'name' => $name,
                'class_name' => $class,
                'slug' => Str::slug($name) . '-' . Str::random(5),
                'description' => "Không gian học tập, trao đổi thông tin chính thức của lớp $class.",
                'avatar_url' => '/uploads/groups/class-default.png',
                'creator_id' => $adminId,
                'privacy' => 'private',
                'join_code' => strtoupper(Str::random(5)),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('social_groups')->insert($groupsToInsert);
        
        // Lấy lại danh sách nhóm đã tạo
        $groups = DB::table('social_groups')->select('id', 'type', 'name', 'class_name')->get();

        $this->command->info('4. Thêm thành viên vào các nhóm...');
        $membersToInsert = [];

        foreach ($groups as $group) {
            if ($group->type === 'class' && isset($studentsByClass[$group->class_name])) {
                // Thêm tất cả sinh viên của lớp đó vào nhóm lớp
                foreach ($studentsByClass[$group->class_name] as $studentId) {
                    $membersToInsert[] = [
                        'group_id' => $group->id,
                        'user_id' => $studentId,
                        'role' => 'member',
                        'joined_at' => now(),
                    ];
                }
            } else {
                // Nhóm cộng đồng: Thêm ngẫu nhiên 50 đến 200 người dùng
                $memberCount = rand(50, 200);
                $randomKeys = array_rand($allUserIds, $memberCount);
                foreach ((array)$randomKeys as $key) {
                    $userId = $allUserIds[$key];
                    $membersToInsert[] = [
                        'group_id' => $group->id,
                        'user_id' => $userId,
                        'role' => ($userId === $adminId) ? 'admin' : 'member',
                        'joined_at' => now(),
                    ];
                }
            }
        }

        // Chunk insert thành viên nhóm
        foreach (array_chunk($membersToInsert, 2000) as $chunk) {
            DB::table('group_members')->insert($chunk);
        }

        // Lưu danh sách thành viên theo nhóm để chọn tác giả bài viết hợp lệ
        $membersByGroup = [];
        $groupMembersData = DB::table('group_members')->select('group_id', 'user_id')->get();
        foreach ($groupMembersData as $gm) {
            $membersByGroup[$gm->group_id][] = $gm->user_id;
        }

        $this->command->info('5. Tạo ~540.000 lượt theo dõi (Follows) - Trung bình 60 lượt/người...');
        $followsToInsert = [];
        $followSet = [];
        $mutualPairs = []; // Lưu các cặp theo dõi chéo nhau
        $targetFollows = 540000;
        $nowString = now()->toDateTimeString();

        // Bước 5a: Tạo các cặp theo dõi chéo (mutual follows) để đảm bảo mỗi người có 20-40 cuộc hội thoại bạn bè
        $mutualFriendCount = [];
        $mutualTargets = [];
        foreach ($allUserIds as $uid) {
            $mutualFriendCount[$uid] = 0;
            $mutualTargets[$uid] = rand(20, 40);
        }

        // Tạo danh sách bạn bè chéo
        foreach ($allUserIds as $uid) {
            $attempts = 0;
            while ($mutualFriendCount[$uid] < $mutualTargets[$uid] && $attempts < 200) {
                $attempts++;
                $friendId = $allUserIds[array_rand($allUserIds)];
                if ($uid === $friendId) continue;

                $key = "$uid-$friendId";
                $oppositeKey = "$friendId-$uid";

                if (!isset($followSet[$key]) && $mutualFriendCount[$friendId] < $mutualTargets[$friendId]) {
                    $followSet[$key] = true;
                    $followSet[$oppositeKey] = true;

                    $followsToInsert[] = [
                        'follower_id' => $uid,
                        'following_id' => $friendId,
                        'created_at' => $nowString,
                    ];
                    $followsToInsert[] = [
                        'follower_id' => $friendId,
                        'following_id' => $uid,
                        'created_at' => $nowString,
                    ];

                    $mutualPairs[] = [$uid, $friendId];
                    $mutualFriendCount[$uid]++;
                    $mutualFriendCount[$friendId]++;

                    if (count($followsToInsert) >= 10000) {
                        DB::table('follows')->insert($followsToInsert);
                        $followsToInsert = [];
                    }
                }
            }
        }

        // Bước 5b: Tạo nốt số lượng lượt theo dõi một chiều còn lại để đạt mục tiêu 540.000 follows
        $attempts = 0;
        while (count($followSet) < $targetFollows && $attempts < 5000000) {
            $attempts++;
            $follower = $allUserIds[array_rand($allUserIds)];
            $following = $allUserIds[array_rand($allUserIds)];
            if ($follower !== $following) {
                $key = "$follower-$following";
                if (!isset($followSet[$key])) {
                    $followSet[$key] = true;
                    $followsToInsert[] = [
                        'follower_id' => $follower,
                        'following_id' => $following,
                        'created_at' => $nowString,
                    ];
                    
                    if (count($followsToInsert) >= 10000) {
                        DB::table('follows')->insert($followsToInsert);
                        $followsToInsert = [];
                    }
                }
            }
        }

        // Chèn nốt số lượng còn lại
        if (!empty($followsToInsert)) {
            DB::table('follows')->insert($followsToInsert);
        }

        unset($followSet);

        $this->command->info('6. Tạo ~10.000 bài viết (Posts) phù hợp với chủ đề nhóm...');
        
        $sportsTemplates = [
            'Tối nay sân cỏ nhân tạo EAUT có kèo đá bóng lúc 19h, đội mình còn thiếu 2 chân sút. Có ai join không?',
            'Giải đấu cầu lông đôi nam nữ EAUT chính thức mở đăng ký rồi kìa anh em ơi! Lập team thôi.',
            'Mới tậu được đôi giày chạy bộ mới ngon phết, chiều nay có ai chạy bộ quanh bờ hồ gần trường không?',
            'Học võ Karate không chỉ nâng cao sức khỏe mà còn tăng khả năng tự vệ. Lớp học tối 2-4-6 nhé cả nhà.',
            'Hôm nay leg day (ngày tập chân), tập xong đi không nổi luôn. Có ai cùng đam mê thể hình không?',
            'Kèo bóng rổ giao lưu chiều thứ 5 tuần này lúc 17h tại sân trường, hoan nghênh tất cả mọi người join!',
            'Tập Taekwondo giúp rèn luyện tính kỷ luật và sự kiên nhẫn. Ai muốn đăng ký tập thử ib mình nha.',
            'Cần tìm sân tập cầu lông gần trường có giá sinh viên chút. Mọi người có địa chỉ nào tốt giới thiệu mình với.',
            'Trận bóng đá giao hữu chiều qua mệt mà vui kinh khủng. Cảm ơn tinh thần thi đấu hết mình của anh em!',
            'Có ai tập Gym ở phòng gần trường không? Tìm bạn tập cùng hỗ trợ đỡ tạ cho nhau đỡ nguy hiểm.'
        ];

        $artTemplates = [
            'Thông báo tuyển thành viên mới cho CLB Âm nhạc nhé cả nhà. Có bạn nào chơi guitar hay piano thì ứng tuyển ngay nha!',
            'Buổi acoustic giao lưu cuối tuần này tại quán cafe đối diện trường, mọi người cùng đến nghe nhạc thư giãn nhé.',
            'Bài nhảy hiện đại mới của CLB mình lên sóng rồi nè mọi người, vào thả tim ủng hộ tụi mình nhé!',
            'Tìm bạn cùng sở thích học piano/guitar từ con số 0. Chúng ta cùng tự học và sửa lỗi cho nhau.',
            'Góc nhiếp ảnh: Hôm nay hoàng hôn ở sân thượng trường EAUT đẹp dã man, mình tranh thủ làm vài shoot hình.',
            'CLB Kịch đang lên kịch bản cho vở diễn chào tân sinh viên khóa tới. Ai muốn thử vai liên hệ ngay nha.',
            'Góc hội họa: Bức tranh phác thảo bằng bút chì đầu tay của mình, mọi người cho xin chút góp ý ạ.',
            'Có ai thích nghe nhạc Indie Việt không? Lập group chia sẻ playlist hay cùng thưởng thức nào.',
            'Buổi tập nhảy chiều nay mệt nhưng cả đội đều rất cố gắng. Hứa hẹn sản phẩm mới sẽ cực kỳ bùng nổ!',
            'CLB Nhiếp ảnh tuần này có buổi offline đi chụp ngoại cảnh ở Hồ Gươm. Ai muốn tham gia đi cùng không?'
        ];

        $techTemplates = [
            'Lập trình Laravel 12 thú vị thật sự, cơ chế Reverb và Realtime hoạt động mượt mà dã man.',
            'Mọi người cho mình hỏi lỗi "500 Internal Server Error" khi deploy project PHP lên hosting sửa thế nào ạ?',
            'Học lập trình quan trọng nhất là tư duy tự giải quyết vấn đề, chứ không chỉ là học thuộc cú pháp ngôn ngữ.',
            'CLB Robotics & IoT tuần này có buổi demo sản phẩm cánh tay robot tự hành tại phòng Lab. Có ai hứng thú qua xem không?',
            'Thời đại của AI phát triển nhanh quá, lập trình viên không tự cập nhật kiến thức liên tục là tụt hậu ngay.',
            'Có ai gặp lỗi khi config Redis với Docker không? Mình kết nối mãi mà toàn bị báo connection refused.',
            'Highly recommend cuốn sách "Clean Code" cho tất cả các bạn sinh viên CNTT nhé. Đọc xong tư duy viết code khác hẳn.',
            'Đang làm dở con app React Native mà dính bug Redux Toolkit debug cả tối chưa ra. Có cao nhân nào rảnh cứu em với!',
            'Cơ chế bảo mật JWT hoạt động như thế nào trong ứng dụng RESTful API vậy mọi người? Ai có tài liệu dễ hiểu xin với.',
            'Làm quen với Git và Github là kỹ năng bắt buộc phải thành thạo đối với mọi lập trình viên khi đi làm.'
        ];

        $langTemplates = [
            'Cần tìm một bạn học nhóm cùng ôn thi IELTS mục tiêu 6.5. Có bạn nào quan tâm thì inbox mình trao đổi nhé.',
            'Buổi sinh hoạt nói tiếng Anh (English Speaking Club) vào sáng Chủ nhật tuần này. Topic: Future of Work.',
            'Chia sẻ bí quyết học từ vựng tiếng Nhật hiệu quả qua flashcard. Ai cần file tài liệu PDF thì comment mình gửi nha.',
            'Lớp học tiếng Hàn giao tiếp cơ bản miễn phí của CLB sẽ bắt đầu từ tuần sau. Đăng ký nhanh kẻo hết chỗ nhé!',
            'Có ai đang chuẩn bị hồ sơ săn học bổng du học Hàn Quốc/Nhật Bản không? Cho mình xin ít kinh nghiệm viết SOP với.',
            'Học tiếng Trung qua bài hát là phương pháp rất thú vị giúp nhớ chữ Hán lâu hơn. Các bạn đã thử chưa?',
            'Kỳ thi JLPT N3 sắp tới rồi, lo lắng quá. Mọi người có bộ đề thi thử các năm trước cho mình xin với.'
        ];

        $volunteerTemplates = [
            'Hội Tình Nguyện trường mình chuẩn bị có chuyến đi phát cháo từ thiện tại bệnh viện vào sáng Chủ nhật tới.',
            'Hãy cùng chung tay làm sạch khuôn viên giảng đường và phân loại rác thải vào ngày Thứ Bảy Xanh tuần này nhé!',
            'Ngày hội hiến máu nhân đạo EAUT sắp diễn ra. Hiến giọt máu đào - Trao đời sự sống, hẹn gặp các bạn tại sảnh lớn!',
            'Chiến dịch mùa hè xanh năm nay hoành tráng lắm, có ai đăng ký tham gia đội hình tình nguyện cùng mình không?',
            'Chuyến đi thiện nguyện vùng cao khép lại với rất nhiều kỷ niệm đẹp. Cảm ơn sự chung tay quyên góp của tất cả các bạn.',
            'CLB chuẩn bị gom quần áo cũ và sách vở quyên góp cho trẻ em nghèo miền núi. Mọi người có đồ cũ mang qua sảnh gửi nha.'
        ];

        $hometownTemplates = [
            'Cuối tuần này có ai về quê Nghệ An/Hà Tĩnh không, mình có xe máy muốn tìm người đi cùng chia tiền xăng.',
            'Họp mặt đồng hương đầu kỳ học mới để chào đón các tân sinh viên khóa dưới. Mọi người sắp xếp thời gian tham gia nhé!',
            'Thèm bát súp lươn Nghệ An/bánh đa xúc hến quá trời. Có quán nào chuẩn vị Nghệ ở gần trường mình không mọi người?',
            'Hội đồng hương mình lập quỹ hỗ trợ các bạn sinh viên khó khăn. Ai cần hỗ trợ hay muốn đóng góp ib ban liên lạc nha.',
            'Có ai quê Thanh Hóa học EAUT lớp mình không nhỉ? Giao lưu kết bạn lập group đồng hương đi xe về quê chung cho vui.',
            'Hội đồng hương Nam Định - Thái Bình làm chuyến liên hoan chào tân sinh viên cuối tuần này nha anh em ơi!'
        ];

        $hobbyTemplates = [
            'Cuối tuần có ai rảnh làm kèo cafe boardgame giao lưu không ạ? Rủ thêm 3-4 bạn nữa cho xôm.',
            'Kèo ma sói tối nay lúc 20h tại sảnh tầng 1, ai tham gia thì chấm dưới bài viết này để mình xếp bài.',
            'Có ai cày Liên Quân hay Tốc Chiến không, lập team leo rank tối nay đi. Mình đi rừng/mid nha.',
            'Review quán bánh mì chảo siêu ngon giá sinh viên ngay cạnh trường. Đồ ăn nhiều, phục vụ nhiệt tình 10/10.',
            'Cần pass lại chiếc bàn học gỗ gấp gọn và chiếc quạt điện mini còn mới 95% giá rẻ cho bạn nào cần.',
            'Nhà trọ khu vực gần trường EAUT hiện tại đang có phòng trống không nhỉ? Mình cần thuê gấp cho đứa em khóa dưới.',
            'Thời tiết Hà Nội hôm nay mát mẻ thích hợp làm một ly trà đá rồi ngồi trò chuyện cả buổi chiều.',
            'Mới đọc xong cuốn sách "Đắc Nhân Tâm", có nhiều bài học rất thấm về kỹ năng giao tiếp ứng xử hằng ngày.',
            'Có ai có kinh nghiệm phượt xe máy Hà Nội - Hà Giang không? Cho mình xin lịch trình và các điểm cần lưu ý với.',
            'Review canteen trường: Món cơm sườn hôm nay ướp ngon xuất sắc, nước sốt đậm đà, rất vừa miệng.'
        ];

        $majorTemplates = [
            'Cơ hội việc làm ngành Logistics trong 2 năm tới thế nào mọi người nhỉ? Có nên đi thực tập từ năm 3 không?',
            'Chia sẻ bộ slide bài giảng chuyên ngành và tài liệu ôn tập hữu ích. Ai cần thì chấm bài viết này nha.',
            'Môn Luật dân sự ôn tập trọng tâm vào phần nào thế các bạn ơi? Đề thi năm ngoái có khó lắm không?',
            'Hội thảo khoa học về xu hướng phát triển của ngành Kỹ thuật Ô tô điện vào sáng thứ Bảy tại Hội trường lớn.',
            'Tìm nhóm làm bài tập lớn môn Quản trị chiến lược/Kế toán doanh nghiệp. Ai còn thiếu thành viên không cho mình join với.',
            'Kỳ thực tập chuyên ngành sắp tới có doanh nghiệp nào về trường tuyển dụng trực tiếp không thầy cô ơi?',
            'Học ngành Dược đòi hỏi sự cẩn thận và kiên trì rất lớn. Mỗi ngày là một núi kiến thức về các gốc hóa học và biệt dược.'
        ];

        $classTemplates = [
            'Mọi người ơi, đề cương ôn thi môn này có phần nào được giảm tải không nhỉ? Hoang mang quá 😭',
            'Thầy cô cho em hỏi quy trình đăng ký phúc khảo điểm thi học phần thì liên hệ phòng ban nào ạ? Em cảm ơn!',
            'Lớp mình hôm nay đi học đầy đủ nhé, nghe đồn hôm nay thầy điểm danh đột xuất đó nha!',
            'Kỳ này lớp mình học 6 môn, lịch thi dày đặc luôn. Chúc cả lớp thi tốt qua môn hết nhé!',
            'Ai có tài liệu slide bài giảng hay đề cương môn này cho mình xin với ạ. Cảm ơn nhiều!',
            'Mới hoàn thành xong buổi báo cáo tiến độ đồ án, áp lực cuối cùng cũng vơi đi một nửa.',
            'Nhắc nhở cả lớp hoàn thành bài tập lớn và nộp trước 23h59 ngày mai nhé các bạn.',
            'Bạn nào chưa nộp học phí kỳ này thì lưu ý hạn chót của nhà trường là cuối tuần này nhé.',
            'Lớp trưởng ơi, có lịch học bù môn học phần tuần sau chưa vậy?',
            'Sách giáo trình môn học phần này viết rất dễ hiểu. Highly recommend cho các bạn đọc thử.'
        ];

        $personalTemplates = [
            'Hôm nay canteen trường mình có món bún chả mới ăn cuốn phết, mọi người đã thử chưa?',
            'Thời tiết Hà Nội hôm nay đẹp quá, chỉ muốn trốn học đi cafe cả ngày thôii.',
            'Một ngày hiệu suất: lên thư viện tự học từ sáng tới chiều, giải quyết xong đống bài tập lớn.',
            'Có ai thấy chìa khóa xe máy wave đỏ ở quanh sảnh tòa nhà A không ạ? Cho mình xin lại với.',
            'Động lực đi học mỗi ngày là được gặp những người bạn siêu dễ thương. Yêu EAUT ghê!',
            'Học kỳ này trôi qua nhanh thật sự, chớp mắt cái đã chuẩn bị thi cuối kỳ rồi.',
            'Chúc mọi người một tuần mới tràn đầy năng lượng và học tập hiệu quả nhé!',
            'Lại một buổi tối cày phim đến 2h sáng, mai đi học lúc 7h không biết có dậy nổi không đây.',
            'EAUT mùa này lá rụng chụp ảnh sống ảo góc nào cũng xinh luôn á mọi người.',
            'Cố gắng mỗi ngày một chút, phiên bản của ngày hôm nay phải tốt hơn phiên bản ngày hôm qua.'
        ];

        $postsToInsert = [];
        $groupIds = $groups->pluck('id')->toArray();
        $groupsMap = $groups->keyBy('id')->toArray();
        $groupCount = count($groupIds);

        // Sinh 10.000 bài viết
        for ($i = 0; $i < 10000; $i++) {
            $inGroup = (rand(1, 100) <= 75); // 75% đăng trong nhóm
            $groupId = $inGroup ? $groupIds[array_rand($groupIds)] : null;
            
            // Tác giả: Nếu đăng trong nhóm, chọn ngẫu nhiên 1 thành viên nhóm, nếu không chọn user bất kỳ
            if ($groupId && isset($membersByGroup[$groupId]) && !empty($membersByGroup[$groupId])) {
                $authorId = $membersByGroup[$groupId][array_rand($membersByGroup[$groupId])];
            } else {
                $authorId = $allUserIds[array_rand($allUserIds)];
                $groupId = null; // Reset nếu nhóm không có thành viên
            }

            // Sinh nội dung phù hợp với chủ đề nhóm
            $content = '';
            if (!$groupId) {
                $content = $personalTemplates[array_rand($personalTemplates)];
            } else {
                $group = $groupsMap[$groupId] ?? null;
                if ($group) {
                    if ($group->type === 'class') {
                        $content = $classTemplates[array_rand($classTemplates)];
                    } else {
                        $nameLower = mb_strtolower($group->name);
                        if (preg_match('/(bóng đá|bóng rổ|bóng chuyền|cầu lông|bóng bàn|võ thuật|karate|taekwondo|vovinam|gym|running|marathon|chạy bộ|yoga|thể thao)/u', $nameLower)) {
                            $content = $sportsTemplates[array_rand($sportsTemplates)];
                        } elseif (preg_match('/(âm nhạc|guitar|piano|nhảy|múa|nhiếp ảnh|media|ảnh|hội họa|mỹ thuật|kịch|kịch bản|radio|podcast|acoustic)/u', $nameLower)) {
                            $content = $artTemplates[array_rand($artTemplates)];
                        } elseif (preg_match('/(lập trình|code|developer|it|cntt|tin học|robotics|iot|ai|machine learning|vi mạch|thiết kế web)/u', $nameLower)) {
                            $content = $techTemplates[array_rand($techTemplates)];
                        } elseif (preg_match('/(tiếng anh|tiếng nhật|tiếng hàn|tiếng trung|ngoại ngữ|du học|học bổng)/u', $nameLower)) {
                            $content = $langTemplates[array_rand($langTemplates)];
                        } elseif (preg_match('/(tình nguyện|hiến máu|từ thiện|môi trường|nhân ái|xã hội)/u', $nameLower)) {
                            $content = $volunteerTemplates[array_rand($volunteerTemplates)];
                        } elseif (preg_match('/(đồng hương|nghệ an|hà tĩnh|thanh hóa|nam định|thái bình|hải dương|bắc ninh|quảng ninh|phú thọ)/u', $nameLower)) {
                            $content = $hometownTemplates[array_rand($hometownTemplates)];
                        } elseif (preg_match('/(logistics|dược khoa|luật|quản trị|kinh doanh|kế toán|cơ khí|chế tạo|xây dựng|thực phẩm|điều dưỡng|ô tô|lữ hành|ecommerce)/u', $nameLower)) {
                            $content = $majorTemplates[array_rand($majorTemplates)];
                        } else {
                            $content = $hobbyTemplates[array_rand($hobbyTemplates)];
                        }
                    }
                } else {
                    $content = $hobbyTemplates[array_rand($hobbyTemplates)];
                }
            }

            $postsToInsert[] = [
                'user_id' => $authorId,
                'group_id' => $groupId,
                'content' => $content,
                'link_url' => (rand(1, 10) === 1) ? 'https://eaut.edu.vn' : null,
                'like_count' => 0, // Cập nhật bằng truy vấn sau
                'reply_count' => 0, // Cập nhật bằng truy vấn sau
                'moderation_status' => 'approved',
                'created_at' => now()->subMinutes(rand(1, 40000)),
            ];
        }

        foreach (array_chunk($postsToInsert, 1000) as $chunk) {
            DB::table('posts')->insert($chunk);
        }

        // Lấy lại danh sách ID bài viết đã tạo
        $postIds = DB::table('posts')->pluck('id')->toArray();
        $postCount = count($postIds);

        $this->command->info('7. Tạo ~10.000 bình luận (Comments)...');
        $vietnameseCommentTemplates = [
            'Đúng vậy bạn ơi, mình cũng đang lo phần này.', 'Cho mình xin link tài liệu với ạ!',
            'Bài viết hữu ích quá, cảm ơn tác giả.', 'Chúc mừng đội tuyển nha, đỉnh quá!',
            'Món đó ngon thật, mình ăn suốt luôn.', 'Inbox mình gửi slide cho nha.',
            'Chúc mọi người thi tốt nha!', 'Hóng kèo cafe cùng bạn.', 'Kỳ này căng quá huhu.',
            'Thầy dạy siêu hay luôn ạ.', 'Đồng ý với quan điểm của bạn.', 'Haha chất quá bạn ơi.',
            'Lớp mình đi học đủ nha anh em.', 'Tuyệt vời quá EAUT ơi!', 'Cố lên sắp ra trường rồi.',
            'Thả tim nha.', 'Cái này dùng thư viện nào vậy bạn?', 'Hay quá bạn ơi!'
        ];

        $commentsToInsert = [];
        for ($i = 0; $i < 10000; $i++) {
            $postId = $postIds[array_rand($postIds)];
            $commentsToInsert[] = [
                'user_id' => $allUserIds[array_rand($allUserIds)],
                'post_id' => $postId,
                'content' => $vietnameseCommentTemplates[array_rand($vietnameseCommentTemplates)],
                'created_at' => now()->subMinutes(rand(1, 30000)),
            ];
        }

        foreach (array_chunk($commentsToInsert, 2000) as $chunk) {
            DB::table('comments')->insert($chunk);
        }

        $this->command->info('8. Tạo ~25.000 lượt thích (Likes)...');
        $likesToInsert = [];
        $likeSet = [];
        $targetLikes = 25000;
        $attempts = 0;
        
        while (count($likeSet) < $targetLikes && $attempts < 100000) {
            $attempts++;
            $post = $postIds[array_rand($postIds)];
            $user = $allUserIds[array_rand($allUserIds)];
            $key = "$user-$post";
            if (!isset($likeSet[$key])) {
                $likeSet[$key] = true;
                $likesToInsert[] = [
                    'user_id' => $user,
                    'post_id' => $post,
                    'created_at' => now(),
                ];
            }
        }

        foreach (array_chunk($likesToInsert, 2000) as $chunk) {
            DB::table('likes')->insert($chunk);
        }

        // Tìm thấy bao nhiêu cặp theo dõi chéo thì tạo bấy nhiêu phòng chat 1-1
        $mutualCount = count($mutualPairs);
        $this->command->info("9. Tạo $mutualCount phòng chat 1-1 từ các cặp theo dõi chéo và 200 phòng chat nhóm...");
        
        $conversationsToInsert = [];
        // Tạo phòng chat 1-1 cho các cặp theo dõi chéo nhau
        for ($i = 0; $i < $mutualCount; $i++) {
            $conversationsToInsert[] = [
                'type' => 'direct',
                'name' => null,
                'created_at' => $nowString,
                'updated_at' => $nowString,
            ];
            
            // Chèn gộp 5000 phòng chat một lần để giải phóng RAM
            if (count($conversationsToInsert) >= 5000) {
                DB::table('conversations')->insert($conversationsToInsert);
                $conversationsToInsert = [];
            }
        }

        // 200 phòng chat nhóm (Group)
        $chatGroupNames = ['Nhóm Học Tập CNTT', 'Kèo Bóng Đá', 'Hội Bạn Thân EAUT', 'Ban Cán Sự Lớp', 'Giao Lưu Học Thuật', 'Kèo Đi Cafe', 'Đội Tình Nguyện K13', 'Nhóm Thiết Kế Slide'];
        for ($i = 0; $i < 200; $i++) {
            $conversationsToInsert[] = [
                'type' => 'group',
                'name' => $chatGroupNames[array_rand($chatGroupNames)] . ' ' . ($i + 1),
                'created_at' => $nowString,
                'updated_at' => $nowString,
            ];
        }

        if (!empty($conversationsToInsert)) {
            DB::table('conversations')->insert($conversationsToInsert);
        }

        $participantsToInsert = [];
        $messagesToInsert = [];

        $chatMessageTemplates = [
            'Alo bạn ơi, rảnh không?', 'Tối nay có đi học không thế?',
            'Cho mình hỏi bài tập nhóm làm đến đâu rồi?', 'Gửi mình slide môn này với.',
            'Ok bạn nhé, lát gặp ở thư viện.', 'Hôm nay thầy điểm danh không nhỉ?',
            'Có ai làm câu 3 đề cương chưa?', 'Đi ăn trưa không cả nhà?',
            'Chúc mừng sinh nhật nhé!', 'Lát check mail gửi file báo cáo nha.',
            'Cảm ơn bạn rất nhiều.', 'Đã nhận được thông tin nhé.',
            'Gặp ở cổng trường nha.', 'Đợi mình 5 phút.', 'Kỳ này đăng ký học phần mấy tín thế?'
        ];

        $directIndex = 0;
        $currentTime = time();

        // Sử dụng chunkById để duyệt qua các phòng chat mà không tải toàn bộ 134.000 đối tượng vào bộ nhớ cùng lúc
        DB::table('conversations')->select('id', 'type')->orderBy('id', 'asc')->chunkById(5000, function ($conversations) use (
            &$directIndex, &$participantsToInsert, &$messagesToInsert, $mutualPairs, $allUserIds, $chatMessageTemplates, $nowString, $currentTime
        ) {
            foreach ($conversations as $conv) {
                $memberIds = [];
                
                if ($conv->type === 'direct') {
                    // Lấy cặp theo dõi chéo tương ứng nếu còn, nếu không fallback chọn ngẫu nhiên
                    if (isset($mutualPairs[$directIndex])) {
                        $pair = $mutualPairs[$directIndex];
                        $directIndex++;
                        $memberIds = [$pair[0], $pair[1]];
                    } else {
                        $k1 = array_rand($allUserIds);
                        $k2 = array_rand($allUserIds);
                        while ($k1 === $k2) {
                            $k2 = array_rand($allUserIds);
                        }
                        $memberIds = [$allUserIds[$k1], $allUserIds[$k2]];
                    }
                    
                    $participantsToInsert[] = [
                        'conversation_id' => $conv->id,
                        'user_id' => $memberIds[0],
                        'role' => 'member',
                        'status' => 'active',
                        'joined_at' => $nowString,
                    ];
                    $participantsToInsert[] = [
                        'conversation_id' => $conv->id,
                        'user_id' => $memberIds[1],
                        'role' => 'member',
                        'status' => 'active',
                        'joined_at' => $nowString,
                    ];
                    
                    // Tin nhắn: 1-3 tin nhắn cho các cuộc hội thoại trực tiếp để tiết kiệm tài nguyên
                    $msgCount = rand(1, 3);
                    for ($m = 0; $m < $msgCount; $m++) {
                        $messagesToInsert[] = [
                            'conversation_id' => $conv->id,
                            'sender_id' => $memberIds[rand(0, 1)],
                            'message_type' => 'text',
                            'content' => $chatMessageTemplates[array_rand($chatMessageTemplates)],
                            'is_read' => 1,
                            'created_at' => date('Y-m-d H:i:s', $currentTime - rand(1, 10000) * 60),
                        ];
                    }
                } else {
                    // Nhóm chat: 5-15 người dùng
                    $groupSize = rand(5, 15);
                    $randomKeys = array_rand($allUserIds, $groupSize);
                    foreach ((array)$randomKeys as $index => $key) {
                        $userId = $allUserIds[$key];
                        $memberIds[] = $userId;
                        $participantsToInsert[] = [
                            'conversation_id' => $conv->id,
                            'user_id' => $userId,
                            'role' => ($index === 0) ? 'admin' : 'member',
                            'status' => 'active',
                            'joined_at' => $nowString,
                        ];
                    }
                    
                    // Tin nhắn: 10-20 tin nhắn cho nhóm chat
                    $msgCount = rand(10, 20);
                    for ($m = 0; $m < $msgCount; $m++) {
                        $messagesToInsert[] = [
                            'conversation_id' => $conv->id,
                            'sender_id' => $memberIds[array_rand($memberIds)],
                            'message_type' => 'text',
                            'content' => $chatMessageTemplates[array_rand($chatMessageTemplates)],
                            'is_read' => 1,
                            'created_at' => date('Y-m-d H:i:s', $currentTime - rand(1, 10000) * 60),
                        ];
                    }
                }

                // Chèn gộp ngay trong vòng lặp nếu vượt quá giới hạn để tránh tràn RAM
                if (count($participantsToInsert) >= 5000) {
                    DB::table('participants')->insert($participantsToInsert);
                    $participantsToInsert = [];
                }
                if (count($messagesToInsert) >= 10000) {
                    DB::table('messages')->insert($messagesToInsert);
                    $messagesToInsert = [];
                }
            }
        });

        // Chèn nốt số lượng còn lại
        if (!empty($participantsToInsert)) {
            DB::table('participants')->insert($participantsToInsert);
        }

        if (!empty($messagesToInsert)) {
            DB::table('messages')->insert($messagesToInsert);
        }

        $this->command->info('9b. Tạo dữ liệu báo cáo (Reports) bài viết, bình luận, tin nhắn và người dùng...');
        $reportsToInsert = [];
        
        // Lấy danh sách ID để làm mẫu báo cáo
        $postIds = DB::table('posts')->pluck('id')->toArray();
        $commentIds = DB::table('comments')->pluck('id')->toArray();
        $messageIds = DB::table('messages')->pluck('id')->toArray();
        
        $reasonsList = ['spam', 'harassment', 'inappropriate', 'violence', 'misinformation', 'privacy', 'other'];
        $statusList = ['pending', 'reviewed', 'resolved', 'dismissed'];
        $detailsTemplates = [
            'Tài khoản này liên tục đăng tải thông tin sai lệch và spam.',
            'Nội dung có chứa ngôn từ thù địch, xúc phạm danh dự người khác.',
            'Có dấu hiệu lừa đảo và dụ dỗ sinh viên tham gia đa cấp.',
            'Hình ảnh không phù hợp với chuẩn mực môi trường đại học.',
            'Spam nội dung quảng cáo dịch vụ thi hộ, viết thuê tiểu luận.',
            'Bình luận quấy rối và sử dụng từ ngữ thô tục vô văn hóa.',
            'Vi phạm quyền riêng tư của tôi khi đăng ảnh chưa được phép.',
            'Nội dung bạo lực kích động xích mích giữa các khóa.'
        ];

        // Tạo 1000 báo cáo ngẫu nhiên
        for ($i = 0; $i < 1000; $i++) {
            $reporterId = $allUserIds[array_rand($allUserIds)];
            $type = ['post', 'comment', 'user'][rand(0, 2)];
            $reportedId = null;

            if ($type === 'post' && !empty($postIds)) {
                $reportedId = $postIds[array_rand($postIds)];
            } elseif ($type === 'comment' && !empty($commentIds)) {
                $reportedId = $commentIds[array_rand($commentIds)];
            } else {
                // Type 'user'
                $reportedId = $allUserIds[array_rand($allUserIds)];
                while ($reportedId === $reporterId) {
                    $reportedId = $allUserIds[array_rand($allUserIds)];
                }
            }

            if ($reportedId) {
                $reportsToInsert[] = [
                    'user_id' => $reporterId,
                    'reported_id' => $reportedId,
                    'type' => $type,
                    'reason' => $reasonsList[array_rand($reasonsList)],
                    'details' => $detailsTemplates[array_rand($detailsTemplates)],
                    'status' => $statusList[array_rand($statusList)],
                    'created_at' => date('Y-m-d H:i:s', $currentTime - rand(1, 10000) * 60),
                    'updated_at' => $nowString,
                ];
            }

            if (count($reportsToInsert) >= 200) {
                DB::table('reports')->insert($reportsToInsert);
                $reportsToInsert = [];
            }
        }

        if (!empty($reportsToInsert)) {
            DB::table('reports')->insert($reportsToInsert);
        }

        $this->command->info('10. Đồng bộ hóa bộ đếm (like_count, reply_count) của bài viết và (follower, following) của người dùng...');
        
        // Chạy các lệnh cập nhật gộp trực tiếp trên cơ sở dữ liệu để đạt tốc độ tối đa
        DB::statement('UPDATE posts p SET like_count = (SELECT COUNT(*) FROM likes WHERE post_id = p.id);');
        DB::statement('UPDATE posts p SET reply_count = (SELECT COUNT(*) FROM comments WHERE post_id = p.id);');
        DB::statement('UPDATE users u SET follower_count = (SELECT COUNT(*) FROM follows WHERE following_id = u.id);');
        DB::statement('UPDATE users u SET following_count = (SELECT COUNT(*) FROM follows WHERE follower_id = u.id);');

        $this->command->info('Dữ liệu mẫu tương tác mạng xã hội khổng lồ đã được tạo thành công!');
    }
}
