@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 700px; margin: 0 auto;">
    <!-- Header -->
    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px;">
        <a href="{{ route('settings') }}" style="color: var(--text-color); text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: var(--glass-bg); border-radius: 12px; border: 1px solid var(--glass-border);">
            <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </a>
        <h2 style="margin: 0; font-size: 24px; font-weight: 800;">Trung tâm trợ giúp</h2>
    </div>

    <!-- FAQ Section -->
    <h3 style="font-size: 18px; font-weight: 700; margin-bottom: 20px; color: var(--text-color);">Câu hỏi thường gặp</h3>

    <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 40px;">
        <div class="faq-card" onclick="toggleFaq(1)">
            <div class="faq-header">
                <span>Làm cách nào để đổi ảnh đại diện?</span>
                <svg id="icon-1" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>
            <div id="faq-1" class="faq-content">
                Bạn có thể đổi ảnh đại diện bằng cách vào trang <strong>Hồ sơ</strong>, chọn <strong>Chỉnh sửa hồ sơ</strong>, sau đó nhấn vào ảnh đại diện hiện tại để tải ảnh mới lên.
            </div>
        </div>

        <div class="faq-card" onclick="toggleFaq(2)">
            <div class="faq-header">
                <span>Tại sao tôi không nhận được mã OTP?</span>
                <svg id="icon-2" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>
            <div id="faq-2" class="faq-content">
                Mã OTP được gửi về email sinh viên của bạn (@eaut.edu.vn). Vui lòng kiểm tra kỹ hòm thư <strong>Spam (Thư rác)</strong>. Mã chỉ có hiệu lực trong 1 phút.
            </div>
        </div>

        <div class="faq-card" onclick="toggleFaq(3)">
            <div class="faq-header">
                <span>Tôi có thể chặn người dùng khác không?</span>
                <svg id="icon-3" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div id="faq-3" class="faq-content">
                Hiện tại chức năng Chặn đang được phát triển. Bạn có thể tạm thời Hủy theo dõi người dùng đó để không thấy bài viết của họ trên bảng tin.
            </div>
        </div>

        <div class="faq-card" onclick="toggleFaq(4)">
            <div class="faq-header">
                <span>Làm thế nào để đăng bài viết mới?</span>
                <svg id="icon-4" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div id="faq-4" class="faq-content">
                Tại trang chủ, bạn hãy nhấn vào ô <strong>"Bạn đang nghĩ gì?"</strong> ở phía trên cùng. Bạn có thể nhập văn bản, đính kèm hình ảnh hoặc video và nhấn <strong>Đăng</strong>.
            </div>
        </div>

        <div class="faq-card" onclick="toggleFaq(5)">
            <div class="faq-header">
                <span>Tôi có thể xóa bài viết đã đăng không?</span>
                <svg id="icon-5" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div id="faq-5" class="faq-content">
                Có, bạn hãy tìm đến bài viết của mình, nhấn vào biểu tượng <strong>ba dấu chấm (...)</strong> ở góc phải bài viết và chọn <strong>Xóa bài viết</strong>.
            </div>
        </div>

        <div class="faq-card" onclick="toggleFaq(6)">
            <div class="faq-header">
                <span>Làm sao để tìm kiếm bạn bè hoặc giảng viên?</span>
                <svg id="icon-6" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div id="faq-6" class="faq-content">
                Sử dụng biểu tượng <strong>Kính lúp</strong> ở thanh điều hướng bên trái. Bạn có thể nhập tên hoặc mã sinh viên/giảng viên để tìm kiếm chính xác.
            </div>
        </div>

        <div class="faq-card" onclick="toggleFaq(7)">
            <div class="faq-header">
                <span>Tại sao tôi không thể gửi tin nhắn cho người khác?</span>
                <svg id="icon-7" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div id="faq-7" class="faq-content">
                Để bảo vệ quyền riêng tư, bạn chỉ có thể nhắn tin cho những người mà <strong>cả hai cùng theo dõi nhau</strong> (Bạn bè). Hãy đảm bảo đối phương cũng đã nhấn theo dõi bạn.
            </div>
        </div>

        <div class="faq-card" onclick="toggleFaq(8)">
            <div class="faq-header">
                <span>Làm cách nào để đổi mật khẩu?</span>
                <svg id="icon-8" viewBox="0 0 24 24" width="18" height="18" stroke="currentColor" stroke-width="2" fill="none"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
            <div id="faq-8" class="faq-content">
                Bạn vào phần <strong>Cài đặt</strong>, chọn <strong>Bảo mật & Mật khẩu</strong>. Tại đây bạn cần nhập mật khẩu cũ và mật khẩu mới để tiến hành thay đổi.
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div style="background: var(--accent-color); padding: 30px; border-radius: 28px; color: white; text-align: center; box-shadow: 0 20px 40px rgba(0, 113, 227, 0.2);">
        <h3 style="margin: 0 0 10px 0; font-size: 20px;">Vẫn cần hỗ trợ?</h3>
        <p style="opacity: 0.9; margin-bottom: 25px; font-size: 15px;">Chúng tôi luôn sẵn sàng lắng nghe ý kiến của bạn.</p>
        <div style="display: flex; justify-content: center;">
            <a href="mailto:support@eaut.edu.vn" style="background: white; color: var(--accent-color); padding: 12px 35px; border-radius: 15px; text-decoration: none; font-weight: 700; font-size: 14px;">Gửi Email hỗ trợ</a>
        </div>
    </div>
</div>

<style>
    .faq-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 18px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .faq-card:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: translateY(-2px);
    }

    .faq-header {
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        color: var(--text-color);
    }

    .faq-header svg {
        transition: transform 0.3s ease;
    }

    .faq-content {
        padding: 0 20px 20px 20px;
        color: var(--secondary-text);
        font-size: 14px;
        line-height: 1.6;
        display: none;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .faq-card.active .faq-content {
        display: block;
    }

    .faq-card.active .faq-header svg {
        transform: rotate(180deg);
    }
</style>

<script>
    function toggleFaq(id) {
        const card = document.getElementById('faq-' + id).parentElement;
        card.classList.toggle('active');
    }
</script>
@endsection