@extends('layouts.app')

@section('content')
<div class="profile-edit-wrapper">
    <div class="container-inner">
        <!-- Header with Back Button -->
        <div class="header-section">
            <a href="{{ route('profile.me') }}" class="back-btn">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
            <h2 class="title">Chỉnh sửa hồ sơ</h2>
        </div>

        @if($errors->any())
        <div class="error-alert">
            <ul class="error-list">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Avatar Upload Section -->
            <div class="glass-card avatar-section">
                <div class="avatar-container">
                    <div id="avatarPreview" class="avatar-image" style="background-image: url('{{ $user->avatar_url }}');"></div>
                    <label for="avatarInput" class="upload-badge">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                    </label>
                </div>
                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(event)">
                <div class="avatar-label">Ảnh đại diện</div>
                <p class="avatar-hint">Tối đa 10MB (Khuyên dùng ảnh vuông)</p>
            </div>

            <!-- Form Fields Group -->
            <div class="form-fields">
                <!-- Username Field -->
                <div class="form-group">
                    <label class="field-label">Tên người dùng</label>
                    <div class="input-wrapper">
                        <span class="prefix">@</span>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" class="text-input">
                    </div>
                </div>

                <!-- Bio Field -->
                <div class="form-group">
                    <label class="field-label">Tiểu sử</label>
                    <div class="input-wrapper">
                        <textarea name="bio" rows="4" placeholder="Viết gì đó về bạn..." class="text-input textarea">{{ old('bio', $user->bio) }}</textarea>
                    </div>
                    <div class="char-count">Tối đa 160 ký tự</div>
                </div>

                <!-- Link URL Field -->
                <div class="form-group">
                    <label class="field-label">Liên kết</label>
                    <div class="input-wrapper">
                        <div class="icon-prefix">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                            </svg>
                        </div>
                        <input type="text" name="link_url" placeholder="Ví dụ: https://facebook.com" value="{{ old('link_url', $user->link_url) }}" class="text-input with-icon">
                    </div>
                </div>

                <!-- Save Button -->
                <div class="action-section">
                    <button type="submit" class="save-btn">
                        <span>Lưu thay đổi</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function previewAvatar(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('avatarPreview');
                output.style.backgroundImage = `url(${reader.result})`;
                output.classList.add('pop-animation');
                setTimeout(() => output.classList.remove('pop-animation'), 400);
            };
            reader.readAsDataURL(file);
        }
    }
</script>

<style>
    :root {
        /* Light Theme (Default) */
        --p-bg: #F8FAFC;
        --p-glass-bg: rgba(255, 255, 255, 0.7);
        --p-glass-border: rgba(0, 98, 255, 0.1);
        --p-text-main: #0F172A;
        --p-text-muted: #64748B;
        --p-accent: #0062FF;
        --p-accent-soft: rgba(0, 98, 255, 0.05);
        --p-card-shadow: 0 20px 50px rgba(0, 98, 255, 0.08);
        --p-input-bg: #FFFFFF;
        --p-btn-bg: #0F172A;
        --p-btn-text: #FFFFFF;
        --p-back-btn-bg: #FFFFFF;
        --p-avatar-border: #FFFFFF;
    }

    /* Dark Theme - Triggered by data-theme="dark" or system preference if app supports it */
    [data-theme="dark"], .dark-mode {
        --p-bg: #0B0F1A;
        --p-glass-bg: rgba(255, 255, 255, 0.03);
        --p-glass-border: rgba(255, 255, 255, 0.08);
        --p-text-main: #FFFFFF;
        --p-text-muted: rgba(255, 255, 255, 0.5);
        --p-accent: #38BDF8;
        --p-accent-soft: rgba(56, 189, 248, 0.1);
        --p-card-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        --p-input-bg: rgba(255, 255, 255, 0.05);
        --p-btn-bg: #FFFFFF;
        --p-btn-text: #000000;
        --p-back-btn-bg: rgba(255, 255, 255, 0.05);
        --p-avatar-border: rgba(255, 255, 255, 0.1);
    }

    /* Fallback for system preference if no data-theme is set */
    @media (prefers-color-scheme: dark) {
        :root:not([data-theme="light"]) {
            --p-bg: #0B0F1A;
            --p-glass-bg: rgba(255, 255, 255, 0.03);
            --p-glass-border: rgba(255, 255, 255, 0.08);
            --p-text-main: #FFFFFF;
            --p-text-muted: rgba(255, 255, 255, 0.5);
            --p-accent: #38BDF8;
            --p-accent-soft: rgba(56, 189, 248, 0.1);
            --p-card-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            --p-input-bg: rgba(255, 255, 255, 0.05);
            --p-btn-bg: #FFFFFF;
            --p-btn-text: #000000;
            --p-back-btn-bg: rgba(255, 255, 255, 0.05);
            --p-avatar-border: rgba(255, 255, 255, 0.1);
        }
    }

    .profile-edit-wrapper {
        min-height: 100vh;
        background: var(--p-bg);
        color: var(--p-text-main);
        padding: 40px 20px;
        font-family: 'Inter', -apple-system, sans-serif;
        transition: background 0.4s ease, color 0.4s ease;
    }

    .container-inner {
        max-width: 550px;
        margin: 0 auto;
    }

    .header-section {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 40px;
    }

    .back-btn {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: var(--p-back-btn-bg);
        border: 1px solid var(--p-glass-border);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--p-text-main);
        text-decoration: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .back-btn:hover {
        transform: translateX(-5px);
        background: var(--p-accent-soft);
        border-color: var(--p-accent);
    }

    .title {
        margin: 0;
        font-size: 26px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .glass-card {
        background: var(--p-glass-bg);
        border: 1px solid var(--p-glass-border);
        border-radius: 32px;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        transition: all 0.4s ease;
    }

    .avatar-section {
        padding: 40px;
        margin-bottom: 35px;
        text-align: center;
        box-shadow: var(--p-card-shadow);
    }

    .avatar-container {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto 20px;
    }

    .avatar-image {
        width: 140px;
        height: 140px;
        background-size: cover;
        background-position: center;
        border-radius: 48px;
        border: 4px solid var(--p-avatar-border);
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .upload-badge {
        position: absolute;
        bottom: -5px;
        right: -5px;
        background: var(--p-accent);
        color: #fff;
        width: 42px;
        height: 42px;
        border-radius: 16px;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        border: 5px solid var(--p-bg);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .upload-badge:hover {
        transform: scale(1.1) rotate(10deg);
        filter: brightness(1.1);
    }

    .avatar-label {
        font-size: 17px;
        font-weight: 700;
        margin-top: 10px;
    }

    .avatar-hint {
        font-size: 13px;
        color: var(--p-text-muted);
        margin-top: 6px;
    }

    .form-fields {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .field-label {
        display: block;
        margin-bottom: 12px;
        font-weight: 700;
        font-size: 13px;
        color: var(--p-text-muted);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        padding-left: 5px;
    }

    .input-wrapper {
        position: relative;
        background: var(--p-input-bg);
        border: 1px solid var(--p-glass-border);
        border-radius: 20px;
        transition: all 0.3s ease;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }

    .input-wrapper:focus-within {
        border-color: var(--p-accent);
        box-shadow: 0 0 0 4px var(--p-accent-soft);
    }

    .input-wrapper.disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background: rgba(0, 0, 0, 0.02);
    }

    .text-input {
        width: 100%;
        background: transparent;
        border: none;
        padding: 18px 22px;
        color: var(--p-text-main);
        font-size: 16px;
        font-weight: 500;
        outline: none;
    }

    .textarea {
        min-height: 120px;
        resize: none;
        line-height: 1.6;
    }

    .prefix {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--p-text-muted);
        font-weight: 700;
        font-size: 18px;
    }

    .input-wrapper .prefix + .text-input {
        padding-left: 45px;
    }

    .icon-prefix {
        position: absolute;
        left: 20px;
        top: 18px;
        color: var(--p-text-muted);
    }

    .with-icon {
        padding-left: 55px;
    }

    .char-count {
        text-align: right;
        font-size: 12px;
        color: var(--p-text-muted);
        margin-top: 8px;
    }

    .action-section {
        margin-top: 20px;
        padding-bottom: 60px;
    }

    .save-btn {
        width: 100%;
        background: var(--p-btn-bg);
        color: var(--p-btn-text);
        border: none;
        padding: 22px;
        border-radius: 24px;
        font-weight: 800;
        font-size: 18px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    }

    .save-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.2);
        opacity: 0.95;
    }

    .error-alert {
        background: rgba(255, 69, 58, 0.1);
        border: 1px solid rgba(255, 69, 58, 0.2);
        color: #FF453A;
        padding: 18px;
        border-radius: 20px;
        margin-bottom: 30px;
    }

    .error-list {
        margin: 0;
        padding-left: 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .pop-animation {
        animation: pop 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes pop {
        0% { transform: scale(0.9); opacity: 0.5; }
        100% { transform: scale(1); opacity: 1; }
    }

    @media (max-width: 600px) {
        .profile-edit-wrapper {
            padding: 20px;
        }
        .avatar-section {
            padding: 30px 20px;
        }
    }
</style>
@endsection