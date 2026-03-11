@extends('layouts.app')

@section('content')
<div class="profile-edit-container" style="padding: 30px; animation: fadeIn 0.4s ease-out;">
    <!-- Header with Back Button -->
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 35px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('profile.me') }}" class="glass-bubble" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; text-decoration: none; color: var(--text-color); transition: all 0.2s;">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            </a>
            <h2 style="margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">Chỉnh sửa hồ sơ</h2>
        </div>
    </div>

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- Avatar Upload Section -->
        <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--glass-border); border-radius: 28px; padding: 30px; margin-bottom: 30px; text-align: center;">
            <div style="position: relative; width: 120px; height: 120px; margin: 0 auto 15px;">
                <div id="avatarPreview" class="avatar" style="width: 100%; height: 100%; background-image: url('{{ $user->avatar_url }}'); background-size: cover; border: 4px solid var(--glass-bg); box-shadow: 0 10px 20px rgba(0,0,0,0.1);"></div>
                <label for="avatarInput" style="position: absolute; bottom: 0; right: 0; background: var(--accent-color); color: white; width: 36px; height: 36px; border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; border: 3px solid var(--glass-bg); box-shadow: 0 4px 10px rgba(0,0,0,0.2); transition: transform 0.2s;">
                    <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                </label>
            </div>
            <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(event)">
            <div style="font-size: 14px; color: var(--secondary-text); font-weight: 500;">Ảnh đại diện</div>
            <p style="font-size: 12px; color: var(--secondary-text); opacity: 0.6; margin-top: 5px;">Hỗ trợ JPG, PNG, GIF. Tối đa 2MB.</p>
        </div>

        <!-- Form Fields Group -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <!-- Username Field -->
            <div class="form-group">
                <label style="display: block; margin-bottom: 10px; font-weight: 700; font-size: 14px; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 0.5px; padding-left: 5px;">Tên người dùng</label>
                <div style="position: relative; display: flex; align-items: center;">
                    <span style="position: absolute; left: 16px; color: var(--secondary-text); font-weight: 600; opacity: 0.5;">@</span>
                    <input type="text" value="{{ $user->username }}" readonly 
                        style="width: 100%; padding: 14px 16px 14px 35px; border: 1px solid var(--glass-border); border-radius: 18px; background: rgba(0,0,0,0.05); color: var(--secondary-text); font-size: 15px; font-weight: 600; outline: none; cursor: not-allowed;"
                        title="Tên người dùng không thể thay đổi">
                </div>
                <div style="font-size: 11px; color: var(--secondary-text); margin-top: 6px; padding-left: 5px; opacity: 0.7;">Tên người dùng đã được tạo tự động và không thể thay đổi.</div>
            </div>

            <!-- Bio Field -->
            <div class="form-group">
                <label style="display: block; margin-bottom: 10px; font-weight: 700; font-size: 14px; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 0.5px; padding-left: 5px;">Tiểu sử</label>
                <textarea name="bio" rows="4" placeholder="Viết gì đó về bạn..." 
                    style="width: 100%; padding: 16px; border: 1px solid var(--glass-border); border-radius: 18px; background: rgba(0,0,0,0.02); color: inherit; font-size: 15px; line-height: 1.5; outline: none; resize: none; transition: all 0.2s;">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <div style="color: #ff3b30; font-size: 12px; margin-top: 6px; padding-left: 5px; font-weight: 600;">{{ $message }}</div>
                @enderror
                <div style="text-align: right; font-size: 11px; color: var(--secondary-text); margin-top: 4px; opacity: 0.5;">Tối đa 160 ký tự</div>
            </div>

            <!-- Link URL Field -->
            <div class="form-group">
                <label style="display: block; margin-bottom: 10px; font-weight: 700; font-size: 14px; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 0.5px; padding-left: 5px;">Các liên kết (Link) - Mỗi dòng một liên kết</label>
                <div style="position: relative; display: flex; align-items: flex-start;">
                    <div style="position: absolute; left: 16px; top: 16px; color: var(--secondary-text); opacity: 0.5;">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                    </div>
                    <textarea name="link_url" rows="3" placeholder="https://facebook.com&#10;https://google.com" 
                        style="width: 100%; padding: 14px 16px 14px 45px; border: 1px solid var(--glass-border); border-radius: 18px; background: rgba(0,0,0,0.02); color: inherit; font-size: 15px; outline: none; transition: all 0.2s; resize: vertical; min-height: 80px;">{{ old('link_url', $user->link_url) }}</textarea>
                </div>
                <div style="font-size: 11px; color: var(--secondary-text); margin-top: 6px; padding-left: 5px; opacity: 0.7;">Nhấn Enter để thêm một liên kết khác.</div>
                @error('link_url')
                    <div style="color: #ff3b30; font-size: 12px; margin-top: 6px; padding-left: 5px; font-weight: 600;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Save Button -->
            <div style="margin-top: 20px;">
                <button type="submit" class="btn-save" style="width: 100%; background: var(--text-color); color: var(--bg-main); border: none; padding: 18px; border-radius: 20px; font-weight: 800; font-size: 16px; cursor: pointer; box-shadow: 0 10px 30px rgba(0,0,0,0.1); transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);">
                    Lưu thay đổi
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .glass-bubble:hover {
        background: rgba(255, 255, 255, 0.5) !important;
        transform: scale(1.05);
    }
    
    input:focus, textarea:focus {
        background: rgba(255, 255, 255, 0.05) !important;
        border-color: var(--accent-color) !important;
        box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.1);
    }

    .btn-save:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        filter: brightness(1.1);
    }

    .btn-save:active {
        transform: translateY(-1px);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    function previewAvatar(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('avatarPreview');
                output.style.backgroundImage = `url(${reader.result})`;
                output.style.animation = 'popIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
            };
            reader.readAsDataURL(file);
        }
    }
</script>

<style>
    @keyframes popIn {
        0% { transform: scale(0.8); opacity: 0.5; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endsection
