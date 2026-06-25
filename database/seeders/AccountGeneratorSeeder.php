<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentDetail;
use App\Models\TeacherDetail;
use App\Models\Faculty;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountGeneratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tối ưu hóa: Băm mật khẩu mặc định 1 lần duy nhất để tái sử dụng
        $defaultPasswordHash = Hash::make('111111');

        $this->command->info('Bắt đầu chuẩn bị dữ liệu sinh viên và giảng viên theo yêu cầu...');

        // Danh sách Họ, Tên đệm và Tên tiếng Việt phổ biến để sinh ngẫu nhiên tên tự nhiên
        $lastNames = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Phan', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý', 'Đinh', 'Lâm'];
        $middleNames = ['Văn', 'Thị', 'Minh', 'Thanh', 'Đức', 'Quang', 'Hữu', 'Kim', 'Ngọc', 'Thu', 'Hồng', 'Phương', 'Khánh', 'Anh', 'Hoàng', 'Quốc'];
        $firstNames = ['Hùng', 'Huy', 'Dũng', 'Nam', 'Hải', 'Tuấn', 'Sơn', 'Tùng', 'Thảo', 'Trang', 'Linh', 'Hương', 'Lan', 'Mai', 'Vy', 'Trực', 'Bình', 'An', 'Khánh', 'Dương', 'Hà', 'Giang', 'Phong', 'Quân'];

        // Helper sinh tên ngẫu nhiên
        $generateName = function() use ($lastNames, $middleNames, $firstNames) {
            $lastName = $lastNames[array_rand($lastNames)];
            $middleName = $middleNames[array_rand($middleNames)];
            $firstName = $firstNames[array_rand($firstNames)];
            return "$lastName $middleName $firstName";
        };

        // Helper chuyển tiếng Việt có dấu sang không dấu
        $removeAccents = function($str) {
            $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
            $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
            $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
            $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
            $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
            $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
            $str = preg_replace("/(đ)/", 'd', $str);
            $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
            $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
            $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
            $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
            $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
            $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
            $str = preg_replace("/(Đ)/", 'D', $str);
            return $str;
        };

        // Helper chuyển tên tiếng Việt có dấu thành CamelCase không dấu (ví dụ: Vũ Văn Huy -> VuVanHuy)
        $convertToCamelCaseUsername = function($fullName) use ($removeAccents) {
            $cleanName = $removeAccents($fullName);
            // Loại bỏ các ký tự đặc biệt, chỉ giữ lại chữ cái và khoảng trắng
            $cleanName = preg_replace('/[^a-zA-Z\s]/', '', $cleanName);
            $parts = preg_split('/\s+/', trim($cleanName));
            $camelParts = array_map(function($part) {
                return ucfirst(strtolower($part));
            }, $parts);
            return implode('', $camelParts);
        };

        // Mảng theo dõi duplicate username
        $usedUsernames = [];

        // Định nghĩa thông tin khoa, mã ngành
        $majorConfig = [
            1 => ['code' => 'CNTT', 'name' => 'Công nghệ thông tin'],
            2 => ['code' => 'DL',   'name' => 'Du lịch'],
            3 => ['code' => 'NN',   'name' => 'Ngôn ngữ'],
            4 => ['code' => 'QTKD', 'name' => 'Quản trị kinh doanh'],
            5 => ['code' => 'TCKT', 'name' => 'Tài chính kế toán'],
            6 => ['code' => 'L',    'name' => 'Luật'],
            7 => ['code' => 'DDD',  'name' => 'Dược điều dưỡng'],
            8 => ['code' => 'LOG',  'name' => 'Logistics'],
            9 => ['code' => 'DDT',  'name' => 'Điện điện tử'],
        ];

        // Tắt Query Log để giải phóng bộ nhớ RAM
        DB::connection()->disableQueryLog();

        $this->command->info('Dọn dẹp các dữ liệu cũ...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        StudentDetail::truncate();
        TeacherDetail::truncate();
        
        DB::table('posts')->truncate();
        DB::table('post_media')->truncate();
        DB::table('comments')->truncate();
        DB::table('likes')->truncate();
        DB::table('follows')->truncate();
        DB::table('participants')->truncate();
        DB::table('messages')->truncate();
        DB::table('appointments')->truncate();
        DB::table('reports')->truncate();
        DB::table('reposts')->truncate();
        DB::table('notifications')->truncate();
        DB::table('group_members')->truncate();
        DB::table('social_groups')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Khởi động giao dịch cơ sở dữ liệu
        DB::transaction(function () use ($defaultPasswordHash, $generateName, $majorConfig, $convertToCamelCaseUsername, &$usedUsernames) {
            
            // 1. Tạo tài khoản Admin
            $this->command->info('Tạo tài khoản Admin...');
            $adminUser = User::create([
                'username' => 'huyberr',
                'email' => 'huyberr@gmail.com',
                'password_hash' => $defaultPasswordHash,
                'role' => 'admin',
                'user_type' => 'teacher',
                'status' => 'active',
                'avatar_url' => '/avatars/default_avatar.jpg', // Dùng avatar chung cho admin
                'bio' => 'Hệ thống Quản trị viên cao cấp & Giảng viên chuyên môn.',
            ]);

            TeacherDetail::create([
                'user_id' => $adminUser->id,
                'full_name' => 'Huyberr Admin',
                'faculty_id' => 1, // CNTT
            ]);

            $usedUsernames['HuyberrAdmin'] = 1;
            $usedUsernames['huyberr'] = 1;

            // 2. Tạo tài khoản Test Student (Nguyễn Văn Huy)
            $this->command->info('Tạo tài khoản Test Student...');
            $testStudentUser = User::create([
                'username' => '20222591',
                'email' => '20222591@eaut.edu.vn',
                'password_hash' => $defaultPasswordHash,
                'role' => 'user',
                'user_type' => 'student',
                'status' => 'active',
                'avatar_url' => '/avatars/default_avatar.jpg', // Dùng avatar chung cho test student
                'bio' => 'Tài khoản sinh viên dùng để test giao diện E-Connect.',
            ]);

            StudentDetail::create([
                'user_id' => $testStudentUser->id,
                'student_id' => '20222591',
                'full_name' => 'Nguyễn Văn Huy',
                'dob' => '2004-01-01',
                'class' => 'CNTT13.01',
                'faculty_id' => 1,
            ]);

            $usedUsernames['NguyenVanHuy'] = 1;
            $usedUsernames['20222591'] = 1;

            // 3. Tạo 200 tài khoản Giảng viên với Email cá nhân và Avatar mặc định chung
            $this->command->info('Tạo 200 tài khoản Giảng viên...');
            $emailDomains = ['gmail.com', 'yahoo.com', 'outlook.com', 'hotmail.com'];

            for ($i = 0; $i < 200; $i++) {
                $name = $generateName();
                $username = $convertToCamelCaseUsername($name);
                
                // Tránh trùng username
                if (!isset($usedUsernames[$username])) {
                    $usedUsernames[$username] = 1;
                } else {
                    $usedUsernames[$username]++;
                    $username .= $usedUsernames[$username];
                }

                // Giảng viên dùng email cá nhân
                $personalEmail = strtolower($username) . '@' . $emailDomains[array_rand($emailDomains)];
                
                // Tránh trùng email
                $emailCheck = User::where('email', $personalEmail)->exists();
                if ($emailCheck) {
                    $personalEmail = strtolower($username) . rand(10, 99) . '@' . $emailDomains[array_rand($emailDomains)];
                }

                $facultyId = ($i % 9) + 1;
                $config = $majorConfig[$facultyId];

                $lecturerUser = User::create([
                    'username' => $username,
                    'email' => $personalEmail,
                    'password_hash' => $defaultPasswordHash,
                    'role' => 'user',
                    'user_type' => 'teacher',
                    'status' => 'active',
                    'avatar_url' => '/avatars/default_avatar.jpg', // Tất cả giảng viên dùng avatar chung
                    'bio' => "Giảng viên Khoa " . $config['name'] . ", Trường Đại học Công nghệ Đông Á.",
                ]);

                TeacherDetail::create([
                    'user_id' => $lecturerUser->id,
                    'full_name' => $name,
                    'faculty_id' => $facultyId,
                ]);
            }

            // 4. Tạo 10000 tài khoản Sinh viên phân phối từ 2022 đến 2025 (2500 sinh viên/năm)
            $this->command->info('Tạo 10000 tài khoản Sinh viên (2500 sinh viên/năm)...');
            
            $years = [2022, 2023, 2024, 2025];
            $studentsPerYear = 2500;
            $classSize = 30; // 30 sinh viên một lớp

            // Theo dõi số sinh viên đã gán cho từng lớp để tính số lớp học (yy)
            $classCounters = [];

            // Để cho phép 1 vài sinh viên học lại năm, ta sẽ lấy khoảng 2.5% (250 sinh viên) làm sinh viên học lại
            $repeatingIndices = [];
            while (count($repeatingIndices) < 250) {
                $val = rand(1, 10000);
                if (!in_array($val, $repeatingIndices)) {
                    $repeatingIndices[] = $val;
                }
            }

            $globalStudentIndex = 0;

            foreach ($years as $year) {
                $this->command->info("Đang tạo sinh viên khóa tuyển sinh năm $year...");

                for ($index = 1; $index <= $studentsPerYear; $index++) {
                    $globalStudentIndex++;

                    // Xác định xem sinh viên này có học lại không
                    $isRepeating = in_array($globalStudentIndex, $repeatingIndices);

                    // Năm nhập học thực tế
                    $entryYear = $year; 

                    // MSSV dạng xxxxyyyy. yyyy là số thứ tự bắt đầu từ 0001
                    $yyyy = str_pad($index, 4, '0', STR_PAD_LEFT);
                    $studentCode = $entryYear . $yyyy;

                    // Chọn khoa theo vòng lặp
                    $facultyId = (($index - 1) % 9) + 1;
                    $config = $majorConfig[$facultyId];

                    // Xác định năm học của lớp (studyYear)
                    $studyYear = $entryYear;
                    if ($isRepeating) {
                        $studyYear = min(2025, $entryYear + rand(1, 2));
                    }

                    // Tính khóa học (xx): 2022 là khóa 13
                    $cohort = $studyYear - 2009; 

                    // Tính lớp học (yy): 30 sinh viên / lớp
                    if (!isset($classCounters[$studyYear][$facultyId])) {
                        $classCounters[$studyYear][$facultyId] = 0;
                    }
                    $studentInClassCount = $classCounters[$studyYear][$facultyId];
                    $classCounter = (int) floor($studentInClassCount / $classSize) + 1;
                    
                    // Format lớp học: CNTT13.01
                    $className = $config['code'] . $cohort . '.' . str_pad($classCounter, 2, '0', STR_PAD_LEFT);

                    // Tăng bộ đếm lớp học
                    $classCounters[$studyYear][$facultyId]++;

                    // Ngày sinh: năm 2022 là 2004 và tiến dần lên (tuổi 18 khi nhập học)
                    $birthYear = $entryYear - 18;
                    $dob = $birthYear . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

                    $name = $generateName();
                    $username = $convertToCamelCaseUsername($name);

                    // Tránh trùng username
                    if (!isset($usedUsernames[$username])) {
                        $usedUsernames[$username] = 1;
                    } else {
                        $usedUsernames[$username]++;
                        $username .= $usedUsernames[$username];
                    }

                    $bio = "Sinh viên lớp $className, Khoa " . $config['name'] . ".";
                    if ($isRepeating) {
                        $bio = "Sinh viên lớp $className (Học lại), Khoa " . $config['name'] . ". MSSV gốc khóa " . ($entryYear - 2009) . ".";
                    }

                    $studentUser = User::create([
                        'username' => $username,
                        'email' => "{$studentCode}@eaut.edu.vn",
                        'password_hash' => $defaultPasswordHash,
                        'role' => 'user',
                        'user_type' => 'student',
                        'status' => 'active',
                        'avatar_url' => '/avatars/default_avatar.jpg', // Tất cả sinh viên dùng avatar chung
                        'bio' => $bio,
                    ]);

                    StudentDetail::create([
                        'user_id' => $studentUser->id,
                        'student_id' => $studentCode,
                        'full_name' => $name,
                        'dob' => $dob,
                        'class' => $className,
                        'faculty_id' => $facultyId,
                    ]);
                }
            }
        });

        $this->command->info('Tạo dữ liệu 10000 sinh viên và 200 giảng viên với avatar chung thành công!');
    }
}
