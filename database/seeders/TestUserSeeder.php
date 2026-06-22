<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\StudentDetail;
use App\Models\Faculty;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TestUserSeeder extends Seeder
{
    /**
     * Tạo tài khoản sinh viên để test giao diện người dùng.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Tạo hoặc cập nhật tài khoản sinh viên test
            $user = User::updateOrCreate(
                ['email' => '20222591@eaut.edu.vn'],
                [
                    'username'       => '20222591',
                    'password_hash'  => Hash::make('111111'),
                    'role'           => 'user',
                    'user_type'      => 'student',
                    'status'         => 'active',
                    'avatar_url'     => '/avatars/user.png',
                    'bio'            => 'Tài khoản sinh viên dùng để test giao diện E-Connect.',
                ]
            );

            // 2. Lấy khoa mặc định (Công nghệ Thông tin - id=1)
            $faculty = Faculty::find(1) ?? Faculty::first();
            if (!$faculty) {
                $faculty = Faculty::create(['name' => 'Công nghệ Thông tin']);
            }

            // 3. Tạo hoặc cập nhật thông tin chi tiết sinh viên
            StudentDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'student_id' => '20222591',
                    'full_name'  => 'Nguyễn Văn Huy',
                    'dob'        => '2004-01-01',
                    'class'      => 'CNTT2022A',
                    'faculty_id' => $faculty->id,
                ]
            );
        });
    }
}
