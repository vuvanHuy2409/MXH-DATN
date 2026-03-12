@extends('layouts.app')

@section('content')
<div class="profile-edit-wrapper" style="min-height: 100vh; background: #000; color: #fff; margin: -20px -20px 0; padding: 30px 20px;">
    <div style="max-width: 600px; margin: 0 auto;">
        <!-- Header with Back Button -->
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 35px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="{{ route('profile.me') }}" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; text-decoration: none; color: #fff; transition: all 0.2s;">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                </a>
                <h2 style="margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">Chỉnh sửa hồ sơ</h2>
            </div>
        </div>

        @if($errors->any())
        <div style="background: rgba(255, 59, 48, 0.15); border: 1px solid rgba(255, 59, 48, 0.3); color: #ff453a; padding: 15px; border-radius: 16px; margin-bottom: 25px; font-size: 14px; font-weight: 600;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Avatar Upload Section -->
            <div style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 28px; padding: 35px; margin-bottom: 30px; text-align: center;">
                <div style="position: relative; width: 130px; height: 120px; margin: 0 auto 15px;">
                    <div id="avatarPreview" class="avatar" style="width: 120px; height: 120px; background-image: url('{{ $user->avatar_url }}'); background-size: cover; border-radius: 40px; border: 3px solid rgba(255,255,255,0.2); box-shadow: 0 10px 30px rgba(0,0,0,0.5);"></div>
                    <label for="avatarInput" style="position: absolute; bottom: -5px; right: 0; background: #fff; color: #000; width: 38px; height: 38px; border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; border: 4px solid #000; box-shadow: 0 4px 15px rgba(0,0,0,0.3); transition: all 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                        <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                            <circle cx="12" cy="13" r="4"></circle>
                        </svg>
                    </label>
                </div>
                <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;" onchange="previewAvatar(event)">
                <div style="font-size: 15px; color: #fff; font-weight: 700;">Ảnh đại diện</div>
                <p style="font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 5px;">Tối đa 10MB (Khuyên dùng ảnh vuông)</p>
            </div>

            <!-- Form Fields Group -->
            <div style="display: flex; flex-direction: column; gap: 28px;">
                <!-- Username Field (Readonly) -->
                <div class="form-group">
                    <label style="display: block; margin-bottom: 12px; font-weight: 700; font-size: 13px; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; padding-left: 5px;">Tên người dùng</label>
                    <div style="position: relative; display: flex; align-items: center;">
                        <span style="position: absolute; left: 18px; color: rgba(255,255,255,0.3); font-weight: 600;">@</span>
                        <input type="text" value="{{ $user->username }}" readonly
                            style="width: 100%; padding: 16px 18px 16px 40px; border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; background: rgba(255,255,255,0.03); color: rgba(255,255,255,0.4); font-size: 16px; font-weight: 600; outline: none; cursor: not-allowed;">
                    </div>
                </div>

                <!-- Bio Field -->
                <div class="form-group">
                    <label style="display: block; margin-bottom: 12px; font-weight: 700; font-size: 13px; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; padding-left: 5px;">Tiểu sử</label>
                    <textarea name="bio" rows="4" placeholder="Viết gì đó về bạn..."
                        style="width: 93.5%; padding: 18px; border: 1px solid rgba(255,255,255,0.15); border-radius: 20px; background: rgba(255,255,255,0.05); color: #fff; font-size: 16px; line-height: 1.6; outline: none; resize: none; transition: all 0.3s;" onfocus="this.style.borderColor='rgba(255,255,255,0.4)'; this.style.background='rgba(255,255,255,0.08)'" onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.background='rgba(255,255,255,0.05)'">{{ old('bio', $user->bio) }}</textarea>
                    <div style="text-align: right; font-size: 11px; color: rgba(255,255,255,0.3); margin-top: 6px;">Tối đa 160 ký tự</div>
                </div>

                <!-- Link URL Field -->
                <div class="form-group">
                    <label style="display: block; margin-bottom: 12px; font-weight: 700; font-size: 13px; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 1px; padding-left: 5px;">Liên kết</label>
                    <div style="position: relative; display: flex; align-items: flex-start;">
                        <div style="position: absolute; left: 18px; top: 18px; color: rgba(255,255,255,0.3);">
                            <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2.5" fill="none">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                            </svg>
                        </div>
                        <textarea name="link_url" rows="3" placeholder="Ví dụ: https://facebook.com"
                            style="width: 100%; padding: 16px 18px 16px 48px; border: 1px solid rgba(255,255,255,0.15); border-radius: 20px; background: rgba(255,255,255,0.05); color: #fff; font-size: 16px; outline: none; transition: all 0.3s; resize: none;" onfocus="this.style.borderColor='rgba(255,255,255,0.4)'; this.style.background='rgba(255,255,255,0.08)'" onblur="this.style.borderColor='rgba(255,255,255,0.15)'; this.style.background='rgba(255,255,255,0.05)'">{{ old('link_url', $user->link_url) }}</textarea>
                    </div>
                </div>

                <!-- Save Button -->
                <div style="margin-top: 20px; padding-bottom: 50px;">
                    <button type="submit" style="width: 100%; background: #fff; color: #000; border: none; padding: 20px; border-radius: 22px; font-weight: 800; font-size: 17px; cursor: pointer; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); box-shadow: 0 10px 30px rgba(255,255,255,0.1);" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 15px 40px rgba(255,255,255,0.15)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 10px 30px rgba(255,255,255,0.1)'">
                        Lưu thay đổi
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
                output.style.animation = 'popIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1)';
            };
            reader.readAsDataURL(file);
        }
    }
</script>

<style>
    @keyframes popIn {
        0% {
            transform: scale(0.8);
            opacity: 0.5;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .profile-edit-wrapper {
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Custom Scrollbar for this page */
    .profile-edit-wrapper::-webkit-scrollbar {
        width: 8px;
    }

    .profile-edit-wrapper::-webkit-scrollbar-track {
        background: #000;
    }

    .profile-edit-wrapper::-webkit-scrollbar-thumb {
        background: #333;
        border-radius: 10px;
    }
</style>
@endsection