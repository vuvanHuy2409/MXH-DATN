# ĐỒ ÁN TỐT NGHIỆP: XÂY DỰNG MẠNG XÃ HỘI NỘI BỘ E-CONNECT

## PHẦN 1: MỞ ĐẦU & BỐI CẢNH ĐỀ TÀI

### 1.1. LỜI MỞ ĐẦU

#### 1.1.1. Tính cấp thiết của đề tài
Trong kỷ nguyên số hóa và sự bùng nổ của Cách mạng Công nghiệp 4.0, chuyển đổi số đã trở thành động lực then chốt thúc đẩy sự phát triển của mọi lĩnh vực trong xã hội, đặc biệt là giáo dục đại học. Môi trường đại học hiện đại không còn giới hạn trong không gian vật lý của giảng đường, giảng đường số và các không gian tương tác trực tuyến đóng vai trò ngày càng quan trọng trong việc hỗ trợ học tập, nghiên cứu và quản lý đào tạo.

Mạng xã hội (Social Network) từ lâu đã chứng minh sức ảnh hưởng to lớn, định hình thói quen giao tiếp, học tập và làm việc của thế hệ trẻ, đặc biệt là sinh viên. Tuy nhiên, các nền tảng mạng xã hội phổ thông hiện nay như Facebook, Zalo, Telegram hay Discord được thiết kế hướng tới đại chúng, phục vụ mục đích giải trí và kết nối phi biên giới. Các nền tảng này hoàn toàn thiếu đi các cơ chế xác thực danh tính chuyên biệt, phân quyền quản trị sư phạm, và khả năng tích hợp sâu sắc với dữ liệu đào tạo của một cơ sở giáo dục.

Tại Trường Đại học Công nghệ Đông Á (EAUT), nhu cầu trao đổi thông tin học thuật, cập nhật thông báo từ các khoa, thảo luận nhóm lớp và tương tác giữa giảng viên với sinh viên là vô cùng lớn và diễn ra liên tục. Việc sử dụng các công cụ giao tiếp công cộng rời rạc dẫn đến tình trạng phân mảnh thông tin, nhiễu loạn bởi tin tức rác và tiềm ẩn nhiều nguy cơ mất an toàn thông tin, rò rỉ dữ liệu nội bộ.

Xuất phát từ thực tiễn trên, đề tài **"Xây dựng mạng xã hội nội bộ E-Connect cho sinh viên và giảng viên Trường Đại học Công nghệ Đông Á"** được lựa chọn nghiên cứu và phát triển. Dự án hướng tới xây dựng một không gian số khép kín, an toàn, mang bản sắc học đường của EAUT, giúp tối ưu hóa công tác trao đổi tri thức và gắn kết cộng đồng học thuật trong nhà trường.

#### 1.1.2. Mục tiêu của đề tài
Mục tiêu cốt lõi của đề tài là nghiên cứu, thiết kế và triển khai một mạng xã hội nội bộ chuyên biệt mang tên E-Connect phục vụ cộng đồng EAUT. Cụ thể bao gồm:
*   **Xây dựng cơ chế xác thực danh tính chặt chẽ:** Triển khai quy trình đăng ký tài khoản bắt buộc sử dụng định dạng email tên miền trường (`@eaut.edu.vn`), kết hợp xác thực OTP (One-Time Password) thời gian thực nhằm đảm bảo tính chính danh và loại bỏ hoàn toàn các tài khoản giả mạo.
*   **Phát triển hệ thống tương tác mạng xã hội:** Cho phép người dùng đăng bài viết đa phương tiện (văn bản, hình ảnh, video, liên kết), thích (Like), đăng lại (Repost), và bình luận phân nhánh sâu (Threaded Comments) để trao đổi sâu về các chủ đề học tập.
*   **Tích hợp giao tiếp thời gian thực (Real-time Communication):** Xây dựng module nhắn tin trực tiếp (Direct Messages), trò chuyện nhóm (Group Chats) kết hợp chia sẻ tài liệu, đặt lịch hẹn học tập (Appointments) sử dụng công nghệ WebSockets (Laravel Reverb) nhằm mang lại phản hồi tức thì.
*   **Tối ưu hóa trải nghiệm người dùng (UX/UI):** Thiết kế giao diện theo phong cách Apple-inspired Glassmorphism (hiệu ứng kính mờ) hiện đại, hỗ trợ chuyển đổi linh hoạt chế độ Dark Mode và đa ngôn ngữ (Tiếng Việt, Tiếng Anh).
*   **Xây dựng phân hệ quản trị toàn diện (Admin Dashboard):** Hỗ trợ nhập dữ liệu người dùng số lượng lớn (Bulk Import) cho sinh viên/giảng viên, quản lý và xử lý nội dung báo cáo vi phạm (Reports), đảm bảo môi trường mạng lành mạnh.

#### 1.1.3. Đối tượng và phạm vi nghiên cứu
*   **Đối tượng nghiên cứu:** 
    *   Các mô hình, kiến trúc và quy trình nghiệp vụ tương tác của mạng xã hội học thuật nội bộ.
    *   Các công nghệ lập trình web hiện đại: Ngôn ngữ PHP (Laravel 12.x), cơ sở dữ liệu MySQL, truyền thông thời gian thực thông qua WebSockets (Laravel Reverb), tương tác Client-side bằng Vanilla JavaScript, và thiết kế CSS Glassmorphism.
*   **Phạm vi nghiên cứu:** 
    *   *Phạm vi nghiệp vụ:* Giới hạn trong các tính năng tương tác mạng xã hội, trò chuyện nhóm/cá nhân, đặt lịch hẹn học thuật, quản lý thông tin thành viên (phân quyền sinh viên/giảng viên/admin) và kiểm duyệt nội dung.
    *   *Phạm vi triển khai:* Thử nghiệm nội bộ dành cho sinh viên và giảng viên thuộc các Khoa của Trường Đại học Công nghệ Đông Á (EAUT).

#### 1.1.4. Phương pháp nghiên cứu
Đồ án áp dụng kết hợp các phương pháp nghiên cứu sau:
1.  **Phương pháp nghiên cứu lý thuyết:** Nghiên cứu tài liệu chuyên ngành về kiến trúc phần mềm, bảo mật hệ thống web, lập trình thời gian thực và trải nghiệm người dùng (UX/UI).
2.  **Phương pháp phân tích hệ thống:** Tiến hành khảo sát nhu cầu tương tác thực tế của sinh viên và giảng viên EAUT để xây dựng sơ đồ Use Case, sơ đồ thực thể mối quan hệ (ERD) và các kịch bản tuần tự (Sequence Diagrams).
3.  **Phương pháp thực nghiệm (Lập trình và thử nghiệm):** Phát triển hệ thống theo mô hình MVC (Model-View-Controller) của Laravel, viết mã nguồn, thiết kế giao diện và tiến hành kiểm thử các chức năng chính để đánh giá hiệu năng và tính bảo mật của ứng dụng.

---

### 1.2. BỐI CẢNH CỦA ĐỀ TÀI

#### 1.2.1. Xu hướng chuyển đổi số trong giáo dục đại học và sự phát triển của MXH học thuật
Trong chiến lược phát triển giáo dục đại học của Việt Nam giai đoạn 2021-2030, chuyển đổi số không chỉ là công cụ hỗ trợ mà đã trở thành giải pháp đột phá để thay đổi cách thức quản lý và nâng cao chất lượng đào tạo. Việc hình thành một "hệ sinh thái học tập số" đòi hỏi các trường đại học phải xây dựng được các kênh tương tác số chính thống, nơi tri thức được chia sẻ và thảo luận một cách dễ dàng, nhanh chóng.

Mạng xã hội học thuật (Academic Social Network) đã và đang phát triển mạnh mẽ trên thế giới (tiêu biểu như ResearchGate, Academia.edu) nhưng chủ yếu phục vụ giới nghiên cứu toàn cầu. Ở quy mô trường học, các mạng xã hội nội bộ đóng vai trò là không gian sinh hoạt trực tuyến kết hợp giữa đời sống sinh viên và hoạt động học tập, giúp tăng cường sự gắn kết giữa nhà trường - giảng viên - sinh viên.

#### 1.2.2. Thực trạng công tác kết nối và tương tác tại Trường Đại học Công nghệ Đông Á (EAUT)
Qua khảo sát thực tế tại Trường Đại học Công nghệ Đông Á, các hoạt động truyền thông và giao tiếp trực tuyến hiện nay đang gặp phải một số bất cập lớn do phụ thuộc quá nhiều vào các ứng dụng bên thứ ba:
*   **Phân mảnh kênh truyền thông:** Thông báo từ nhà trường được đăng trên website; trao đổi của giảng viên với lớp diễn ra trên MS Teams; hoạt động của lớp hành chính lại thảo luận qua Zalo; trong khi các sự kiện ngoại khóa hay câu lạc bộ lại nằm trên các nhóm Facebook. Sinh viên phải theo dõi đồng thời quá nhiều ứng dụng, dẫn đến tình trạng bỏ lỡ thông báo quan trọng.
*   **Hạn chế của các công cụ liên lạc hiện tại:**
    *   *Zalo:* Giới hạn lưu trữ tệp tin (tệp tin học tập thường bị xóa sau một thời gian ngắn), số lượng thành viên nhóm hạn chế, thông tin quan trọng dễ bị trôi và loãng bởi tin nhắn tự do.
    *   *Facebook Groups & Messenger:* Môi trường giải trí quá lớn khiến sinh viên dễ mất tập trung khi học tập. Nguy hiểm hơn, thuật toán ưu tiên quảng cáo và đề xuất có thể dẫn đến việc rò rỉ thông tin cá nhân.
    *   *MS Teams & Email:* Phục vụ tốt cho các buổi học trực tuyến hoặc gửi thông tin chính thức mang tính một chiều, nhưng thiếu đi tính tương tác xã hội mềm dẻo, gần gũi, khiến sinh viên e ngại trong việc chủ động kết nối với giảng viên.
*   **Rủi ro an ninh mạng và xác thực danh tính:** Trên các nhóm Facebook hay Zalo của lớp/khoa, bất kỳ tài khoản cá nhân nào cũng có thể tham gia mà không cần qua bước kiểm duyệt danh tính thực tế. Điều này dẫn đến nguy cơ xuất hiện các đối tượng xấu giả danh sinh viên hoặc giảng viên để phát tán thông tin sai lệch, lừa đảo tài chính hoặc đánh cắp tài liệu nội bộ của trường.

#### 1.2.3. Sự cần thiết của giải pháp E-Connect
Nhằm giải quyết triệt để các tồn tại trên, việc thiết lập mạng xã hội nội bộ E-Connect là bước đi tất yếu và vô cùng cần thiết đối với EAUT, mang lại các giá trị cốt lõi:
1.  **Chính danh và Khép kín (Security & Identity):** Bằng việc sử dụng hệ thống xác thực email trường `@eaut.edu.vn` qua OTP, E-Connect tạo dựng một cộng đồng người dùng thực 100%. Mọi hành vi trên hệ thống đều gắn liền với danh tính thực tế (Sinh viên hoặc Giảng viên thuộc lớp/khoa cụ thể), giúp nâng cao trách nhiệm cá nhân của mỗi thành viên khi phát ngôn và chia sẻ thông tin.
2.  **Tập trung hóa thông tin (Centralized Information):** E-Connect đóng vai trò là điểm chạm thông tin duy nhất cho đời sống học đường của sinh viên. Từ việc cập nhật thông báo của khoa, thảo luận bài tập nhóm lớp, trao đổi tài liệu học tập đến nhắn tin trò chuyện với bạn bè đều được thực hiện trên cùng một nền tảng.
3.  **Tích hợp công nghệ hiện đại và mượt mà:**
    *   *Laravel Reverb (WebSockets):* Đảm bảo các thông báo (thích, bình luận, tin nhắn mới) được truyền tải tức thì mà không cần tải lại trang, tăng cường tính gắn kết của trải nghiệm tương tác trực tuyến.
    *   *Thiết kế Glassmorphism:* Tạo cảm giác hiện đại, trẻ trung, phù hợp với thẩm mỹ của thế hệ sinh viên Gen Z, đồng thời cung cấp chế độ Dark Mode bảo vệ thị lực khi học tập vào ban đêm.
    *   *Quản trị thông minh:* Ban quản lý nhà trường và admin có thể kiểm soát chất lượng nội dung thông qua hệ thống lọc báo cáo vi phạm, quản lý tài khoản linh hoạt, đảm bảo môi trường học thuật luôn lành mạnh và văn minh.

Có thể khẳng định, sự ra đời của **E-Connect** không chỉ giải quyết bài toán giao tiếp trực tuyến hiệu quả mà còn góp phần thúc đẩy tiến trình xây dựng văn hóa học đường số văn minh, hiện đại tại Trường Đại học Công nghệ Đông Á (EAUT).
