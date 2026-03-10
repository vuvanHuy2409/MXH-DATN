@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 600px; margin: 0 auto;">
    <h2 style="margin-top: 0; margin-bottom: 25px; font-size: 28px; font-weight: 800;">Cài đặt</h2>

    @if(session('status'))
        <div style="background: rgba(52, 199, 89, 0.1); color: #28a745; padding: 12px; border-radius: 12px; font-size: 14px; margin-bottom: 20px; border: 1px solid rgba(52, 199, 89, 0.2); animation: fadeIn 0.3s;">
            {{ session('status') }}
        </div>
    @endif

    <!-- Nhóm: Tài khoản -->
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 14px; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-left: 5px;">Tài khoản</h3>
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <!-- Chỉnh sửa hồ sơ -->
            <div onclick="window.location.href='{{ route('profile.me') }}'" class="settings-item">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(0, 113, 227, 0.1); color: #0071e3;">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <span>Chỉnh sửa hồ sơ</span>
                </div>
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>

            <!-- Đổi mật khẩu -->
            <div onclick="openPasswordModal()" class="settings-item">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(255, 149, 0, 0.1); color: #ff9500;">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    </div>
                    <span>Bảo mật & Mật khẩu</span>
                </div>
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </div>
    </div>

    <!-- Nhóm: Trải nghiệm -->
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 14px; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-left: 5px;">Trải nghiệm</h3>
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <!-- Chế độ tối -->
            <div onclick="toggleTheme()" class="settings-item">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(88, 86, 214, 0.1); color: #5856d6;">
                        <span id="settings-theme-icon">
                            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                        </span>
                    </div>
                    <span>Chế độ tối</span>
                </div>
                <div style="width: 40px; height: 22px; background: #34c759; border-radius: 20px; position: relative; transition: all 0.3s;">
                    <div id="theme-switch-thumb" style="width: 18px; height: 18px; background: white; border-radius: 50%; position: absolute; top: 2px; left: 20px; transition: all 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nhóm: Giới thiệu -->
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 14px; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-left: 5px;">Giới thiệu</h3>
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <!-- Về sản phẩm -->
            <div onclick="openAboutProductModal()" class="settings-item">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(0, 113, 227, 0.1); color: #0071e3;">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    </div>
                    <span>Về sản phẩm</span>
                </div>
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>

            <!-- Về tác giả -->
            <div onclick="openAboutAuthorModal()" class="settings-item">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(255, 45, 85, 0.1); color: #ff2d55;">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    </div>
                    <span>Về tác giả</span>
                </div>
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </div>
    </div>

    <!-- Nhóm: Hỗ trợ -->
    <div style="margin-bottom: 40px;">
        <h3 style="font-size: 14px; color: var(--secondary-text); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-left: 5px;">Hỗ trợ</h3>
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <div onclick="window.location.href='{{ route('settings.help') }}'" class="settings-item">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(52, 199, 89, 0.1); color: #34c759;">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <span>Trung tâm trợ giúp</span>
                </div>
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
            
            <div class="settings-item">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(142, 142, 147, 0.1); color: #8e8e93;">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    </div>
                    <span>Về E-Connect</span>
                </div>
                <span style="font-size: 14px; color: var(--secondary-text);">v1.0.0</span>
            </div>

            <div onclick="openFeedbackModal()" class="settings-item">
                <div class="settings-item-left">
                    <div class="icon-box" style="background: rgba(0, 122, 255, 0.1); color: #007aff;">
                        <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </div>
                    <span>Đóng góp ý kiến</span>
                </div>
                <svg viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </div>
        </div>
    </div>

    <!-- Đăng xuất -->
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" style="width: 100%; padding: 18px; background: rgba(255, 59, 48, 0.1); border: none; color: #ff3b30; border-radius: 18px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 12px; font-size: 16px; transition: all 0.2s;">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            Đăng xuất
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
            <h3 style="margin: 0; font-size: 22px; font-weight: 800;">Đổi mật khẩu</h3>
            <p style="color: var(--secondary-text); font-size: 14px; margin-top: 5px;">Bảo vệ tài khoản của bạn</p>
        </div>
        
        <form action="{{ route('settings.change-password') }}" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label class="input-label">Mật khẩu hiện tại</label>
                <input type="password" name="current_password" required class="modern-input">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label class="input-label">Mật khẩu mới</label>
                <input type="password" name="new_password" required class="modern-input">
            </div>
            
            <div style="margin-bottom: 25px;">
                <label class="input-label">Xác nhận mật khẩu</label>
                <input type="password" name="new_password_confirmation" required class="modern-input">
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="button" onclick="closePasswordModal()" style="flex: 1; padding: 15px; border-radius: 18px; border: 1px solid var(--glass-border); background: transparent; font-weight: 600; cursor: pointer;">Hủy</button>
                <button type="submit" class="btn-post" style="flex: 2; padding: 15px; border-radius: 18px; font-weight: 700;">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<!-- Feedback Modal -->
<div id="feedbackModal" class="modal" style="display: none; background: rgba(0,0,0,0.2); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); align-items: center; justify-content: center;">
    <div class="modal-content glass-bubble" style="max-width: 500px; padding: 35px; border-radius: 35px; border: 1px solid rgba(255,255,255,0.5); width: 90%;">
        <div style="text-align: center; margin-bottom: 25px;">
            <div style="width: 60px; height: 60px; background: rgba(0, 122, 255, 0.1); color: #007aff; border-radius: 20px; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="2.5" fill="none"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            </div>
            <h3 style="margin: 0; font-size: 22px; font-weight: 800;">Đóng góp ý kiến</h3>
            <p style="color: var(--secondary-text); font-size: 14px; margin-top: 5px;">Ý kiến của bạn giúp chúng tôi hoàn thiện hơn</p>
        </div>
        
        <form action="{{ route('settings.feedback') }}" method="POST">
            @csrf
            <div style="margin-bottom: 25px;">
                <label class="input-label">Nội dung phản hồi</label>
                <textarea name="content" required class="modern-input" style="min-height: 150px; resize: none; padding: 15px;" placeholder="Hãy chia sẻ cảm nhận hoặc báo lỗi bạn gặp phải..."></textarea>
                <div style="font-size: 11px; color: var(--secondary-text); margin-top: 8px; text-align: right;">Tối thiểu 10 ký tự</div>
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="button" onclick="closeFeedbackModal()" style="flex: 1; padding: 15px; border-radius: 18px; border: 1px solid var(--glass-border); background: transparent; font-weight: 600; cursor: pointer;">Hủy</button>
                <button type="submit" class="btn-post" style="flex: 2; padding: 15px; border-radius: 18px; font-weight: 700;">Gửi phản hồi</button>
            </div>
        </form>
    </div>
</div>

<!-- About Product Modal -->
<div id="aboutProductModal" class="modal" style="display: none; background: rgba(0,0,0,0.2); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); align-items: center; justify-content: center;">
    <div class="modal-content glass-bubble" style="max-width: 500px; padding: 35px; border-radius: 35px; border: 1px solid rgba(255,255,255,0.5); width: 90%;">
        <div style="text-align: center; margin-bottom: 25px;">
            <div style="width: 60px; height: 60px; background: rgba(0, 113, 227, 0.1); color: #0071e3; border-radius: 20px; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center;">
                <svg viewBox="0 0 24 24" width="30" height="30" stroke="currentColor" stroke-width="2.5" fill="none"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            </div>
            <h3 style="margin: 0; font-size: 22px; font-weight: 800;">Về E-Connect</h3>
        </div>
        <div style="line-height: 1.6; color: var(--text-color); font-size: 15px;">
            <p><strong>E-Connect</strong> là một mạng xã hội học đường hiện đại, được thiết kế chuyên biệt để kết nối cộng đồng sinh viên và giảng viên.</p>
            <p>Sứ mệnh của chúng tôi là tạo ra một môi trường giao lưu học thuật năng động, nơi kiến thức được chia sẻ dễ dàng và các mối quan hệ học thuật được thắt chặt hơn thông qua công nghệ.</p>
            <p>Các tính năng chính:</p>
            <ul style="padding-left: 20px; margin-top: 10px;">
                <li>Chia sẻ bài viết, tài liệu và kiến thức.</li>
                <li>Hệ thống tin nhắn thời gian thực (Cá nhân & Nhóm).</li>
                <li>Quản lý nhóm học tập và cộng đồng.</li>
                <li>Thông báo tức thì về các hoạt động mới.</li>
            </ul>
        </div>
        <button type="button" onclick="closeAboutProductModal()" style="width: 100%; padding: 15px; border-radius: 18px; border: 1px solid var(--glass-border); background: var(--accent-color); color: white; font-weight: 700; cursor: pointer; margin-top: 25px;">Đóng</button>
    </div>
</div>

<!-- About Author Modal -->
<div id="aboutAuthorModal" class="modal" style="display: none; background: rgba(0,0,0,0.2); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); align-items: center; justify-content: center;">
    <div class="modal-content glass-bubble" style="max-width: 450px; padding: 35px; border-radius: 35px; border: 1px solid rgba(255,255,255,0.5); width: 90%;">
        <div style="text-align: center; margin-bottom: 25px;">
            <div style="width: 80px; height: 80px; background: rgba(255, 45, 85, 0.1); color: #ff2d55; border-radius: 50%; margin: 0 auto 15px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                <svg viewBox="0 0 24 24" width="40" height="40" stroke="currentColor" stroke-width="2" fill="none"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <h3 style="margin: 0; font-size: 22px; font-weight: 800;">Thông tin Tác giả</h3>
        </div>
        <div style="line-height: 1.6; color: var(--text-color); font-size: 15px; text-align: center;">
            <p>Sản phẩm được phát triển với niềm đam mê và tâm huyết nhằm mang lại giá trị thiết thực cho cộng đồng giáo dục.</p>
            <div style="margin-top: 20px; padding: 15px; background: rgba(0,0,0,0.02); border-radius: 15px; border: 1px solid var(--glass-border);">
                <div style="font-weight: 700; color: var(--secondary-text); font-size: 12px; text-transform: uppercase; margin-bottom: 5px;">Liên hệ với tôi</div>
                <div style="font-size: 16px; font-weight: 800; color: var(--accent-color);">huyberr@gmail.com</div>
            </div>
        </div>
        <button type="button" onclick="closeAboutAuthorModal()" style="width: 100%; padding: 15px; border-radius: 18px; border: 1px solid var(--glass-border); background: transparent; font-weight: 700; cursor: pointer; margin-top: 25px;">Đóng</button>
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
    function openFeedbackModal() {
        document.getElementById('feedbackModal').style.display = 'flex';
        document.body.classList.add('modal-open');
    }
    function closeFeedbackModal() {
        document.getElementById('feedbackModal').style.display = 'none';
        document.body.classList.remove('modal-open');
    }

    function openAboutProductModal() {
        document.getElementById('aboutProductModal').style.display = 'flex';
        document.body.classList.add('modal-open');
    }
    function closeAboutProductModal() {
        document.getElementById('aboutProductModal').style.display = 'none';
        document.body.classList.remove('modal-open');
    }
    function openAboutAuthorModal() {
        document.getElementById('aboutAuthorModal').style.display = 'flex';
        document.body.classList.add('modal-open');
    }
    function closeAboutAuthorModal() {
        document.getElementById('aboutAuthorModal').style.display = 'none';
        document.body.classList.remove('modal-open');
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
