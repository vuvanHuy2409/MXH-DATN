<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentDetail;
use App\Models\TeacherDetail;
use App\Models\Faculty;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AccountGeneratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tối ưu hóa: Băm mật khẩu mặc định 1 lần duy nhất để tái sử dụng
        $defaultPasswordHash = Hash::make('111111');

        $this->command->info('Bắt đầu chuẩn bị dữ liệu sinh viên và giảng viên...');

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

        // Mảng theo dõi duplicate email prefix của Giảng viên
        $usedLecturerPrefixes = [];

        // Helper sinh email prefix cho giảng viên
        $generateLecturerPrefix = function($fullName) use ($removeAccents, &$usedLecturerPrefixes) {
            $cleanName = $removeAccents($fullName);
            $parts = preg_split('/\s+/', trim($cleanName));
            if (empty($parts)) {
                $parts = ['giangvien'];
            }
            
            $firstName = strtolower(array_pop($parts));
            $initials = '';
            foreach ($parts as $part) {
                if (!empty($part)) {
                    $initials .= strtolower(mb_substr($part, 0, 1));
                }
            }
            
            $basePrefix = $firstName . $initials;
            
            if (!isset($usedLecturerPrefixes[$basePrefix])) {
                $usedLecturerPrefixes[$basePrefix] = 1;
            } else {
                $usedLecturerPrefixes[$basePrefix]++;
            }
            
            return $basePrefix . $usedLecturerPrefixes[$basePrefix];
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

        // Mảng theo dõi duplicate username của Sinh viên
        $usedStudentUsernames = [];

        // Định nghĩa thông tin khoa, mã ngành và thời gian đào tạo (Graduation Duration)
        $majorConfig = [
            1 => ['code' => 'CNTT', 'duration' => 4],
            2 => ['code' => 'DL',   'duration' => 4],
            3 => ['code' => 'NN',   'duration' => 4],
            4 => ['code' => 'QTKD', 'duration' => 4],
            5 => ['code' => 'TCKT', 'duration' => 4],
            6 => ['code' => 'L',    'duration' => 4],
            7 => ['code' => 'DDD',  'duration' => 5],
            8 => ['code' => 'LOG',  'duration' => 4],
            9 => ['code' => 'DDT',  'duration' => 4],
        ];

        // Tắt Query Log để giải phóng bộ nhớ RAM trong quá trình sinh 9000 bản ghi
        DB::connection()->disableQueryLog();

        $this->command->info('Dọn dẹp các dữ liệu cũ liên quan đến tài khoản...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        StudentDetail::truncate();
        TeacherDetail::truncate();
        
        // Dọn các bảng liên quan khác tránh lỗi khóa ngoại
        DB::table('posts')->truncate();
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
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Khởi động giao dịch cơ sở dữ liệu để đạt hiệu năng tối đa và đảm bảo toàn vẹn dữ liệu
        DB::transaction(function () use ($defaultPasswordHash, $generateName, $generateLecturerPrefix, $majorConfig, $convertToCamelCaseUsername, &$usedStudentUsernames) {
            $this->command->info('1. Tạo tài khoản Admin...');
            $adminUser = User::create([
                'username' => 'huyberr',
                'email' => 'huyberr@gmail.com',
                'password_hash' => $defaultPasswordHash,
                'role' => 'admin',
                'user_type' => 'teacher',
                'status' => 'active',
                'bio' => 'Hệ thống Quản trị viên cao cấp.',
            ]);

            TeacherDetail::create([
                'user_id' => $adminUser->id,
                'full_name' => 'Huyberr Admin',
                'faculty_id' => 1, // CNTT
            ]);

            $this->command->info('2. Tạo 500 tài khoản Giảng viên...');
            for ($i = 0; $i < 500; $i++) {
                $name = $generateName();
                $prefix = $generateLecturerPrefix($name);
                
                $lecturerUser = User::create([
                    'username' => $prefix,
                    'email' => "$prefix@eaut.edu.vn",
                    'password_hash' => $defaultPasswordHash,
                    'role' => 'user',
                    'user_type' => 'teacher',
                    'status' => 'active',
                    'bio' => 'Giảng viên Trường Đại học Công nghệ Đông Á.',
                ]);

                TeacherDetail::create([
                    'user_id' => $lecturerUser->id,
                    'full_name' => $name,
                    'faculty_id' => ($i % 9) + 1, // Chia đều 9 khoa
                ]);
            }

            $this->command->info('3. Tạo 8.499 tài khoản Sinh viên phân phối từ 2022 đến 2026...');
            
            // Phân phối sinh viên theo các năm tuyển sinh
            $enrollmentDist = [
                2022 => 1700,
                2023 => 1700,
                2024 => 1700,
                2025 => 1700,
                2026 => 1699,
            ];

            // Để tính toán lớp học: theo dõi số lượng sinh viên đã tạo cho từng (năm tuyển sinh, khoa)
            // Cấu trúc: $classCounters[$year][$facultyId] = số sinh viên đã gán
            $classCounters = [];

            foreach ($enrollmentDist as $year => $totalStudentsForYear) {
                $this->command->info("Đang tạo sinh viên khóa tuyển sinh năm $year ($totalStudentsForYear sinh viên)...");
                
                // Mapped Cohort
                // 2022 -> Khóa 13
                $cohort = $year - 2009; 

                for ($index = 1; $index <= $totalStudentsForYear; $index++) {
                    // Mã yyyy bắt đầu từ 0001
                    $yyyy = str_pad($index, 4, '0', STR_PAD_LEFT);
                    $studentCode = $year . $yyyy;

                    // Chọn khoa theo vòng lặp tuần hoàn để phân phối đều
                    $facultyId = (($index - 1) % 9) + 1;
                    $config = $majorConfig[$facultyId];

                    // Tính lớp học: 40 sinh viên / lớp
                    if (!isset($classCounters[$year][$facultyId])) {
                        $classCounters[$year][$facultyId] = 0;
                    }
                    $studentInMajorCount = $classCounters[$year][$facultyId];
                    $classCounter = (int) floor($studentInMajorCount / 40) + 1;
                    $className = $config['code'] . ' ' . $cohort . '.' . $classCounter;

                    // Tăng bộ đếm sinh viên trong khoa cho năm đó
                    $classCounters[$year][$facultyId]++;

                    // Sinh Ngày sinh hợp lý (Sinh viên nhập học ở tuổi 18)
                    $birthYear = $year - 18;
                    $dob = $birthYear . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);

                    $name = $generateName();

                    // Tạo username CamelCase duy nhất
                    $username = $convertToCamelCaseUsername($name);
                    if (!isset($usedStudentUsernames[$username])) {
                        $usedStudentUsernames[$username] = 1;
                    } else {
                        $usedStudentUsernames[$username]++;
                        $username .= $usedStudentUsernames[$username];
                    }

                    $studentUser = User::create([
                        'username' => $username,
                        'email' => "{$studentCode}@eaut.edu.vn",
                        'password_hash' => $defaultPasswordHash,
                        'role' => 'user',
                        'user_type' => 'student',
                        'status' => 'active',
                        'bio' => "Sinh viên lớp $className, Khoa " . $config['code'] . ".",
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

        $this->command->info('Quá trình sinh dữ liệu 9.000 tài khoản đã hoàn thành xuất sắc!');
    }
}
