@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 600px; margin: 0 auto;">
    <h2 style="margin-top: 0; margin-bottom: 25px; font-size: 28px; font-weight: 800;">{{ __('Settings') }}</h2>

    @if(session('status'))
        <div style="background: rgba(52, 199, 89, 0.1); color: #28a745; padding: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 20px; border: 1px solid rgba(52, 199, 89, 0.2); animation: fadeIn 0.3s;">
            {{ session('status') }}
        </div>
    @endif

    <!-- Nhóm: Tài khoản -->
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 14px; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-left: 5px;">{{ __('Account') }}</h3>
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <!-- Chỉnh sửa hồ sơ -->
            <div onclick="window.location.href='{{ route('profile.me') }}'" class="settings-item">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(0, 113, 227, 0.1); color: #0071e3;">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <span>{{ __('Edit Profile') }}</span>
                </div>
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>

            <!-- Đổi mật khẩu -->
            <div onclick="openPasswordModal()" class="settings-item">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(255, 149, 0, 0.1); color: #ff9500;">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <span>{{ __('Security & Password') }}</span>
                </div>
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </div>
    </div>

    <!-- Nhóm: Trải nghiệm -->
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 14px; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-left: 5px;">{{ __('Experience') }}</h3>
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <!-- Chế độ tối -->
            <div onclick="toggleTheme()" class="settings-item">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(88, 86, 214, 0.1); color: #5856d6;">
                        <span id="settings-theme-icon">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                        </span>
                    </div>
                    <span>{{ __('Dark Mode') }}</span>
                </div>
                <div style="width: 40px; height: 22px; background: #34c759; border-radius: 20px; position: relative; transition: all 0.3s;">
                    <div id="theme-switch-thumb" style="width: 18px; height: 18px; background: white; border-radius: 50%; position: absolute; top: 2px; left: 20px; transition: all 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"></div>
                </div>
            </div>

            <!-- Ngôn ngữ -->
            <div class="settings-item" onclick="toggleLanguageDropdown()">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(255, 45, 85, 0.1); color: #ff2d55;">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                    </div>
                    <span>{{ __('Language') }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 14px; color: var(--secondary-text);">{{ App::getLocale() == 'vi' ? 'Tiếng Việt' : 'English' }}</span>
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
                </div>
            </div>
            
            <!-- Language Selector (Hidden by default) -->
            <div id="languageDropdown" style="display: none; background: rgba(255,255,255,0.05); border-radius: 15px; padding: 10px; margin-top: -5px; animation: slideDown 0.3s;">
                <form action="{{ route('settings.change-language') }}" method="POST" id="langForm">
                    @csrf
                    <input type="hidden" name="locale" id="localeInput">
                    <div onclick="submitLang('vi')" style="padding: 12px; border-radius: 10px; display: flex; justify-content: space-between; cursor: pointer; {{ App::getLocale() == 'vi' ? 'background: rgba(0,113,227,0.1); color: #0071e3;' : '' }}">
                        <span>Tiếng Việt</span>
                        @if(App::getLocale() == 'vi') <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> @endif
                    </div>
                    <div onclick="submitLang('en')" style="padding: 12px; border-radius: 10px; display: flex; justify-content: space-between; cursor: pointer; {{ App::getLocale() == 'en' ? 'background: rgba(0,113,227,0.1); color: #0071e3;' : '' }}">
                        <span>English</span>
                        @if(App::getLocale() == 'en') <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="3" fill="none"><polyline points="20 6 9 17 4 12"></polyline></svg> @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Nhóm: Hỗ trợ -->
    <div style="margin-bottom: 40px;">
        <h3 style="font-size: 14px; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-left: 5px;">{{ __('Support') }}</h3>
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <div class="settings-item">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(52, 199, 89, 0.1); color: #34c759;">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <span>{{ __('Help Center') }}</span>
                </div>
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
            
            <div class="settings-item">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(142, 142, 147, 0.1); color: #8e8e93;">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    </div>
                    <span>{{ __('About E-Connect') }}</span>
                </div>
                <span style="font-size: 14px; color: var(--secondary-text);">v1.0.0</span>
            </div>
        </div>
    </div>

    <!-- Đăng xuất -->
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="width: 100%; padding: 18px; background: rgba(255, 59, 48, 0.1); border: none; color: #ff3b30; border-radius: 18px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 12px; font-size: 16px; transition: all 0.2s;">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            {{ __('Logout') }}
        </button>
    </form>
</div>

<!-- Password Modal -->
<div id="passwordModal" class="modal" style="display: none; background: rgba(0,0,0,0.2); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); align-items: center; justify-content: center;">
    <div class="modal-content glass-bubble" style="max-width: 400px; padding: 35px; border-radius: 35px; border: 1px solid rgba(255,255,255,0.5);">
        <div style="text-align: center; margin-bottom: 25px;">
            <div style="width: 60px; height: 60px; background: rgba(255, 149, 0, 0.1); color: #ff9500; border-radius: 20px; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="2.5" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
            <h3 style="margin: 0; font-size: 22px; font-weight: 800;">{{ __('Change Password') }}</h3>
            <p style="color: var(--secondary-text); font-size: 14px; margin-top: 5px;">{{ __('Bảo vệ tài khoản của bạn') }}</p>
        </div>
        
        <form action="{{ route('settings.change-password') }}" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label class="input-label">{{ __('Current Password') }}</label>
                <input type="password" name="current_password" required class="modern-input">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label class="input-label">{{ __('New Password') }}</label>
                <input type="password" name="new_password" required class="modern-input">
            </div>
            
            <div style="margin-bottom: 25px;">
                <label class="input-label">{{ __('Confirm Password') }}</label>
                <input type="password" name="new_password_confirmation" required class="modern-input">
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="button" onclick="closePasswordModal()" style="flex: 1; padding: 15px; border-radius: 18px; border: 1px solid var(--glass-border); background: transparent; font-weight: 600; cursor: pointer;">{{ __('Cancel') }}</button>
                <button type="submit" class="btn-post" style="flex: 2; padding: 15px; border-radius: 18px; font-weight: 700;">{{ __('Update') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPasswordModal() {
        document.getElementById('passwordModal').style.display = 'flex';
        document.body.classList.add('modal-open');
    }
    function closePasswordModal() {
        document.getElementById('passwordModal').style.display = 'none';
        document.body.classList.remove('modal-open');
    }
    function toggleLanguageDropdown() {
        const dropdown = document.getElementById('languageDropdown');
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
    }
    function submitLang(lang) {
        document.getElementById('localeInput').value = lang;
        document.getElementById('langForm').submit();
    }
</script>

<style>
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .settings-item {
        background: var(--glass-bg);
        padding: 18px 20px;
        border-radius: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--glass-border);
    }
    .settings-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        background: rgba(255, 255, 255, 0.9);
    }
    .settings-item-left {
        display: flex;
        align-items: center;
        gap: 15px;
        font-weight: 600;
        font-size: 15px;
    }
    .icon-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .input-label {
        display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: var(--secondary-text); padding-left: 5px;
    }
    .modern-input {
        width: 100%; padding: 14px; border-radius: 14px; border: 1px solid var(--glass-border); background: rgba(0,0,0,0.02); outline: none; transition: all 0.2s; box-sizing: border-box;
    }
    .modern-input:focus {
        background: #fff; border-color: var(--accent-color); box-shadow: 0 0 0 4px rgba(0, 113, 227, 0.1);
    }
</style>
@endsection
