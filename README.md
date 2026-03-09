# E-Connect - Mạng Xã Hội Sinh Viên EAUT 🚀

**E-Connect** là một nền tảng mạng xã hội nội bộ dành riêng cho sinh viên và giảng viên Trường Đại học Công nghệ Đông Á (EAUT). Dự án được thiết kế với phong cách hiện đại (Apple Glassmorphism), tập trung vào sự tối giản, mượt mà và khả năng kết nối cộng đồng học thuật.

---

## 👨‍🎓 Thông tin Sinh viên thực hiện
*   **Họ và tên:** [Tên của bạn]
*   **Mã sinh viên:** [Mã sinh viên của bạn]
*   **Lớp:** [Lớp của bạn]
*   **Khoa:** Công nghệ thông tin
*   **Trường:** Đại học Công nghệ Đông Á (EAUT)
*   **Đồ án:** Đồ án tốt nghiệp / Bài tập lớn chuyên ngành

---

## 🛠 Công nghệ sử dụng (Tech Stack)

### Backend
*   **Framework:** Laravel 12.x
*   **Language:** PHP 8.x
*   **Database:** MySQL
*   **Real-time:** Laravel Reverb (WebSockets)
*   **Authentication:** Custom Auth with OTP Verification

### Frontend
*   **Template Engine:** Blade
*   **Styling:** Vanilla CSS (Phong cách Glassmorphism)
*   **Interactions:** Vanilla JavaScript
*   **Localization:** Đa ngôn ngữ (Việt - Anh)

---

## 🌟 Chức năng chính (Features)

### 1. Hệ thống Tài khoản & Bảo mật
*   **Đăng ký/Đăng nhập:** Xác thực qua email tên miền `@eaut.edu.vn`.
*   **Xác thực OTP:** Gửi mã xác thực về email thực tế (hiệu lực 60s).
*   **Phân quyền:** Tài khoản sinh viên và giảng viên với thông tin chi tiết riêng biệt.
*   **Đổi mật khẩu:** Chức năng đổi mật khẩu an toàn trong phần cài đặt.

### 2. Tương tác Mạng xã hội
*   **Đăng bài (Posts):** Hỗ trợ đăng văn bản, hình ảnh, video và liên kết.
*   **Tương tác:** Thích (Like), Đăng lại (Repost), Bình luận (Comment) theo dạng Thread.
*   **Tìm kiếm:** Tìm kiếm người dùng và bài viết nhanh chóng.
*   **Hồ sơ:** Trang cá nhân hiển thị bài viết, danh sách người theo dõi.

### 3. Giao tiếp & Thông báo
*   **Nhắn tin (Direct Messages):** Trò chuyện trực tiếp giữa các người dùng.
*   **Thông báo (Notifications):** Nhận thông báo thời gian thực khi có người thích, bình luận hoặc theo dõi.

### 4. Tùy chỉnh cá nhân (Settings)
*   **Dark Mode:** Chế độ nền tối bảo vệ mắt.
*   **Đa ngôn ngữ:** Chuyển đổi linh hoạt giữa Tiếng Việt và Tiếng Anh.
*   **Cửa sổ chào mừng:** Giao diện chào mừng thân thiện khi đăng ký thành công.

---

## 📸 Giao diện ứng dụng
Ứng dụng sử dụng ngôn ngữ thiết kế **Glassmorphism** với các đặc điểm:
*   Nền mờ (Backdrop blur) 25px.
*   Viền kính trắng mỏng (Glass border).
*   Bóng đổ mềm mại (Soft shadows).
*   Hoạt ảnh mượt mà (Smooth animations).

---

## 🚀 Hướng dẫn cài đặt

1. **Clone dự án:**
   ```bash
   git clone [link-git-cua-ban]
   cd mxh_DATN
   ```

2. **Cài đặt thư viện:**
   ```bash
   composer install
   npm install
   ```

3. **Cấu hình môi trường:**
   *   Sao chép `.env.example` thành `.env`.
   *   Cấu hình `DB_DATABASE`, `MAIL_USERNAME`, `MAIL_PASSWORD`.

4. **Khởi tạo dữ liệu:**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

5. **Chạy ứng dụng:**
   ```bash
   php artisan serve
   ```

---

## 📝 Nội dung phát triển (Roadmap)
- [x] Hệ thống OTP qua Email.
- [x] Giao diện đa ngôn ngữ.
- [x] Chế độ Dark Mode.
- [ ] Tích hợp Chat GPT hỗ trợ giải đáp học tập.
- [ ] Chức năng nhóm học tập (Study Groups).

---
© 2026 E-Connect Project. Được thực hiện bởi Sinh viên EAUT.
