<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Register') }} - EAUT Social</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main: #F8FAFC;
            --glass-bg: rgba(255, 255, 255, 0.88);
            --glass-border: #E2E8F0;
            --text-color: #1d1d1f;
            --secondary-text: #6e6e73;
            --accent-color: #0062FF;
        }

        [data-theme="dark"] {
            --bg-main: #0a0a0a;
            --glass-bg: rgba(28, 28, 30, 0.8);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-color: #f5f5f7;
            --secondary-text: #98989d;
            --accent-color: #0a84ff;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            background-image: radial-gradient(at 0% 0%, rgba(0, 98, 255, 0.06) 0, transparent 50%),
                radial-gradient(at 100% 100%, rgba(226, 232, 240, 0.6) 0, transparent 50%);
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
            color: var(--text-color);
            transition: background-color 0.3s, color 0.3s;
        }

        [data-theme="dark"] body {
            background-image: radial-gradient(at 0% 0%, hsla(240, 10%, 15%, 1) 0, transparent 50%),
                radial-gradient(at 100% 100%, hsla(240, 10%, 10%, 1) 0, transparent 50%);
        }

        .theme-toggle {
            position: fixed;
            top: 25px;
            right: 25px;
            width: 45px;
            height: 45px;
            border-radius: 15px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--text-color);
            backdrop-filter: blur(10px);
            z-index: 1000;
            transition: all 0.3s;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
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
            position: relative;
        }

        [data-theme="dark"] .register-container {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .logo {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin: 0 auto 20px;
            overflow: hidden;
            border: 2px solid var(--glass-border);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05) rotate(3deg);
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        h1 {
            font-size: 28px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 30px;
            letter-spacing: -1px;
            color: var(--text-color);
        }

        .type-selector {
            display: flex;
            background: rgba(0, 0, 0, 0.05);
            padding: 5px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        [data-theme="dark"] .type-selector {
            background: rgba(255, 255, 255, 0.05);
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
            background: var(--glass-bg);
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
            background: rgba(255, 255, 255, 0.2);
            font-size: 15px;
            box-sizing: border-box;
            outline: none;
            transition: all 0.3s;
            color: var(--text-color);
        }

        [data-theme="dark"] input,
        [data-theme="dark"] select {
            background: rgba(0, 0, 0, 0.2);
        }

        input:focus,
        select:focus {
            background: var(--glass-bg);
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
            background: var(--text-color);
            color: var(--bg-main);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .btn-register:hover {
            opacity: 0.9;
            transform: translateY(-2px);
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
    <div class="theme-toggle" id="themeToggle" onclick="toggleTheme()">
        <!-- Icon will be injected by JS -->
    </div>

    <div class="register-container">
        <div class="logo">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>
        <h1>{{ __('Create Account') }}</h1>
        <div class="type-selector">
            <div onclick="setType('student')" id="btn-student" class="type-option active">{{ __('Student') }}</div>
            <div onclick="setType('teacher')" id="btn-teacher" class="type-option">{{ __('Teacher') }}</div>
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
                <label>{{ __('Full Name') }}</label>
                <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="{{ __('Enter full name') }}" required autofocus>
            </div>

            <div class="form-group">
                <label>{{ __('Faculty') }}</label>
                <select name="faculty_id" id="faculty_id" required>
                    <option value="">-- {{ __('Select Faculty') }} --</option>
                    @foreach($faculties as $faculty)
                    <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>{{ $faculty->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Student Fields -->
            <div id="student-fields">
                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label>{{ __('Date of Birth') }}</label>
                        <input type="date" name="dob" value="{{ old('dob') }}">
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>{{ __('Class') }}</label>
                        <input type="text" name="class" value="{{ old('class') }}" list="class-list" placeholder="{{ __('Select class') }}...">
                        <datalist id="class-list">
                            @for($i = 1; $i <= 22; $i++) <option value="DCCNTT13.10.{{ $i }}"> @endfor
                        </datalist>
                    </div>
                </div>
                <div class="form-group">
                    <label>{{ __('Student Email (8-digit ID)') }}</label>
                    <div class="email-input-wrapper">
                        <input type="text" name="student_id_prefix" value="{{ old('student_id_prefix') }}" maxlength="8" pattern="\d{8}">
                        <span class="email-suffix">@eaut.edu.vn</span>
                    </div>
                </div>
            </div>

            <!-- Teacher Fields -->
            <div id="teacher-fields" style="display: none;">
                <div class="form-group">
                    <label>{{ __('Teacher Email (Personal or School)') }}</label>
                    <div class="email-input-wrapper">
                        <input type="email" name="teacher_email" value="{{ old('teacher_email') }}" placeholder="example@gmail.com">
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>{{ __('Password') }}</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>{{ __('Confirm') }}</label>
                    <input type="password" name="password_confirmation" required>
                </div>
            </div>

            <button type="submit" class="btn-register">{{ __('Register Now') }}</button>
        </form>

        <div class="footer-links">{{ __('Already have an account?') }} <a href="{{ route('login') }}">{{ __('Login') }}</a></div>
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

        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const targetTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', targetTheme);
            localStorage.setItem('theme', targetTheme);
            updateThemeIcon(targetTheme);
        }

        function updateThemeIcon(theme) {
            const toggle = document.getElementById('themeToggle');
            const sunIcon = '<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>';
            const moonIcon = '<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>';
            toggle.innerHTML = theme === 'dark' ? sunIcon : moonIcon;
        }

        // Initialize theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        updateThemeIcon(savedTheme);
    </script>
</body>

</html>
