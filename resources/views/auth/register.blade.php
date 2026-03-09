<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - EAUT Social</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main: #D1E9F6;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
            --text-color: #1d1d1f;
            --secondary-text: #6e6e73;
            --accent-color: #0071e3;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }

        .register-container {
            width: 100%;
            max-width: 500px;
            padding: 40px;
            background: var(--glass-bg);
            backdrop-filter: blur(30px);
            -webkit-backdrop-filter: blur(30px);
            border: 1px solid var(--glass-border);
            border-radius: 35px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 28px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: -1px;
        }

        .type-selector {
            display: flex;
            background: rgba(0, 0, 0, 0.05);
            padding: 5px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        .type-option {
            flex: 1;
            text-align: center;
            padding: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            border-radius: 12px;
            transition: all 0.2s;
            color: var(--secondary-text);
        }

        .type-option.active {
            background: #fff;
            color: var(--text-color);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            margin-left: 5px;
            color: var(--secondary-text);
        }

        input,
        select {
            width: 100%;
            padding: 12px 18px;
            border-radius: 14px;
            border: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.5);
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
            transition: all 0.3s;
        }

        input:focus,
        select:focus {
            background: #fff;
            border-color: var(--accent-color);
        }

        .email-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .email-input-wrapper input {
            padding-right: 130px;
        }

        .email-suffix {
            position: absolute;
            right: 18px;
            color: var(--secondary-text);
            font-weight: 600;
            font-size: 15px;
            pointer-events: none;
        }

        .btn-register {
            width: 100%;
            padding: 16px;
            border-radius: 18px;
            border: none;
            background: #000;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .footer-links {
            margin-top: 25px;
            text-align: center;
            font-size: 14px;
            color: var(--secondary-text);
        }

        .footer-links a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 700;
        }

        .error-list {
            background: rgba(255, 59, 48, 0.1);
            color: #ff3b30;
            padding: 15px;
            border-radius: 15px;
            font-size: 13px;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h1>Tạo tài khoản</h1>
        <div class="type-selector">
            <div onclick="setType('student')" id="btn-student" class="type-option active">Sinh viên</div>
            <div onclick="setType('teacher')" id="btn-teacher" class="type-option">Giáo viên</div>
        </div>

        @if($errors->any())
        <div class="error-list">
            <ul>@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <input type="hidden" name="user_type" id="user_type" value="student">

            <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="Nhập họ và tên thật" required autofocus>
            </div>

            <div class="form-group">
                <label>Khoa</label>
                <select name="faculty_id" id="faculty_id" required>
                    <option value="">-- Chọn khoa --</option>
                    @foreach($faculties as $faculty)
                    <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>{{ $faculty->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Student Fields -->
            <div id="student-fields">
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Ngày sinh</label>
                        <input type="date" name="dob" value="{{ old('dob') }}">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Lớp học</label>
                        <input type="text" name="class" value="{{ old('class') }}" list="class-list" placeholder="Chọn lớp...">
                        <datalist id="class-list">
                            @for($i = 1; $i <= 22; $i++) <option value="DCCNTT13.10.{{ $i }}"> @endfor
                        </datalist>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email sinh viên (8 số MSV)</label>
                    <div class="email-input-wrapper">
                        <input type="text" name="student_id_prefix" value="{{ old('student_id_prefix') }}" maxlength="8" pattern="\d{8}">
                        <span class="email-suffix">@eaut.edu.vn</span>
                    </div>
                </div>
            </div>

            <!-- Teacher Fields -->
            <div id="teacher-fields" style="display: none;">
                <div class="form-group">
                    <label>Email giáo viên (Tên viết tắt)</label>
                    <div class="email-input-wrapper">
                        <input type="text" name="teacher_email_prefix" value="{{ old('teacher_email_prefix') }}">
                        <span class="email-suffix">@eaut.edu.vn</span>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Xác nhận</label>
                    <input type="password" name="password_confirmation" required>
                </div>
            </div>

            <button type="submit" class="btn-register">Đăng ký ngay</button>
        </form>

        <div class="footer-links">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></div>
    </div>

    <script>
        function setType(type) {
            document.getElementById('user_type').value = type;
            document.getElementById('btn-student').classList.toggle('active', type === 'student');
            document.getElementById('btn-teacher').classList.toggle('active', type === 'teacher');
            document.getElementById('student-fields').style.display = type === 'student' ? 'block' : 'none';
            document.getElementById('teacher-fields').style.display = type === 'teacher' ? 'block' : 'none';

            const studentInputs = document.querySelectorAll('#student-fields input');
            const teacherInputs = document.querySelectorAll('#teacher-fields input');
            studentInputs.forEach(i => i.required = (type === 'student'));
            teacherInputs.forEach(i => i.required = (type === 'teacher'));
        }
        @if(old('user_type') === 'teacher') setType('teacher');
        @endif
    </script>
</body>

</html>