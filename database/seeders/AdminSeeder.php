<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TeacherDetail;
use App\Models\Faculty;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Tạo hoặc cập nhật tài khoản User Admin
            $user = User::updateOrCreate(
                ['email' => 'huyberr@gmail.com'],
                [
                    'username' => 'huyberr',
                    'password_hash' => Hash::make('tôi quên mật khẩu sau'),
                    'role' => 'admin',
                    'user_type' => 'teacher', // Thiết lập là Giảng viên
                    'status' => 'active',
                    'avatar_url' => '/avatars/1772179976_4.jpg', // Dùng một ảnh có sẵn trong public/avatars
                    'bio' => 'Quản trị viên hệ thống & Giảng viên chuyên môn.',
                ]
            );

            // 2. Đảm bảo có ít nhất một khoa để gán cho giảng viên
            $faculty = Faculty::first();
            if (!$faculty) {
                $faculty = Faculty::create(['name' => 'Công nghệ Thông tin']);
            }

            // 3. Tạo hoặc cập nhật thông tin chi tiết Giảng viên (TeacherDetail)
            TeacherDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name' => 'Huyberr Admin',
                    'faculty_id' => $faculty->id,
                ]
            );
        });
    }
}
