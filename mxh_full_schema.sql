-- ============================================================
-- DATABASE: mxh_datn  (E-Connect Social Network - DATN)
-- Phiên bản: v2 - Thiết kế lại theo ERD & tài liệu
-- ============================================================

CREATE DATABASE IF NOT EXISTS mxh_datn
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE mxh_datn;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- Bảng: faculties
-- Mô tả: Danh sách các khoa trong trường
-- ============================================================
CREATE TABLE `faculties` (
  `id`   bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100)        NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `faculties` (`id`, `name`) VALUES
  (1, 'Công nghệ thông tin'),
  (2, 'Du lịch'),
  (3, 'Ngôn ngữ'),
  (4, 'Quản trị kinh doanh'),
  (5, 'Tài chính kế toán'),
  (6, 'Luật'),
  (7, 'Dược điều dưỡng'),
  (8, 'Logistics'),
  (9, 'Điện điện tử');

-- ============================================================
-- Bảng: users
-- Mô tả: Tài khoản người dùng (sinh viên & giảng viên)
-- Thay đổi so v1: thêm cột `role`
-- ============================================================
CREATE TABLE `users` (
  `id`              bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username`        varchar(50)         NOT NULL,
  `email`           varchar(100)        NOT NULL,
  `password_hash`   varchar(255)        NOT NULL,
  `role`            enum('user','admin','moderator') NOT NULL DEFAULT 'user',
  `user_type`       enum('student','teacher')        NOT NULL,
  `avatar_url`      varchar(255)        DEFAULT '/avatars/user.png',
  `bio`             varchar(160)        DEFAULT NULL,
  `link_url`        text                DEFAULT NULL,
  `follower_count`  int(10) UNSIGNED    DEFAULT 0,
  `following_count` int(10) UNSIGNED    DEFAULT 0,
  `status`          enum('active','banned') DEFAULT 'active',
  `is_private`      tinyint(1)          NOT NULL DEFAULT 0,
  `created_at`      timestamp           NULL DEFAULT NULL,
  `updated_at`      timestamp           NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique`    (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users`
  (`id`,`username`,`email`,`password_hash`,`role`,`user_type`,`avatar_url`,`bio`,`link_url`,
   `follower_count`,`following_count`,`status`,`is_private`,`created_at`,`updated_at`)
VALUES
  (2,'vuthithao','20222591@eaut.edu.vn',
   '$2y$12$7gjPk8y9w6Cl5nXn0bTf8e8yJIFCXkCDcrHCb13KkkiOACn0VWDL6',
   'user','teacher','/avatars/1773123695_2.jpeg',
   '20222591 \r\nhuyberr@gmail.com\r\nhãy gọi cho tôi',
   'https://www.youtube.com/watch?v=XhUXUYEwYf0&list=RD4mtpDkVUE8w&index=3',
   0,0,'active',0,'2026-03-09 08:40:18','2026-03-10 06:21:35');

-- ============================================================
-- Bảng: sessions
-- Mô tả: Phiên đăng nhập (SESSION_DRIVER=database)
-- ============================================================
CREATE TABLE `sessions` (
  `id`            varchar(255) NOT NULL,
  `user_id`       bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address`    varchar(45)  DEFAULT NULL,
  `user_agent`    text         DEFAULT NULL,
  `payload`       longtext     NOT NULL,
  `last_activity` int(11)      NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index`       (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: student_details
-- Mô tả: Thông tin chi tiết sinh viên (quan hệ 0..1 với users)
-- ============================================================
CREATE TABLE `student_details` (
  `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(20)         NOT NULL,
  `full_name`  varchar(100)        NOT NULL,
  `dob`        date                DEFAULT NULL,
  `class`      varchar(50)         NOT NULL,
  `faculty_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sd_student_id_unique` (`student_id`),
  CONSTRAINT `sd_user_id_foreign`    FOREIGN KEY (`user_id`)    REFERENCES `users`     (`id`) ON DELETE CASCADE,
  CONSTRAINT `sd_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: teacher_details
-- Mô tả: Thông tin chi tiết giảng viên (quan hệ 0..1 với users)
-- ============================================================
CREATE TABLE `teacher_details` (
  `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    bigint(20) UNSIGNED NOT NULL,
  `full_name`  varchar(100)        NOT NULL,
  `faculty_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `td_user_id_foreign`    FOREIGN KEY (`user_id`)    REFERENCES `users`     (`id`) ON DELETE CASCADE,
  CONSTRAINT `td_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `teacher_details` (`user_id`, `full_name`, `faculty_id`) VALUES (2, 'Vũ Thị Thảo', 1);

-- ============================================================
-- Bảng: follows
-- Mô tả: Quan hệ theo dõi n-n giữa users và users
-- ============================================================
CREATE TABLE `follows` (
  `follower_id`  bigint(20) UNSIGNED NOT NULL,
  `following_id` bigint(20) UNSIGNED NOT NULL,
  `created_at`   timestamp           NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`follower_id`, `following_id`),
  CONSTRAINT `follows_follower_id_foreign`  FOREIGN KEY (`follower_id`)  REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `follows_following_id_foreign` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: social_groups
-- Mô tả: Nhóm cộng đồng / lớp học
-- ============================================================
CREATE TABLE `social_groups` (
  `id`          bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`        enum('community','class') NOT NULL DEFAULT 'community',
  `name`        varchar(255)        NOT NULL,
  `class_name`  varchar(255)        DEFAULT NULL,
  `slug`        varchar(255)        NOT NULL,
  `description` text                DEFAULT NULL,
  `avatar_url`  varchar(255)        DEFAULT NULL,
  `cover_url`   varchar(255)        DEFAULT NULL,
  `creator_id`  bigint(20) UNSIGNED NOT NULL,
  `privacy`     enum('public','private') NOT NULL DEFAULT 'public',
  `join_code`   varchar(5)          DEFAULT NULL,
  `created_at`  timestamp           NULL DEFAULT NULL,
  `updated_at`  timestamp           NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `social_groups_slug_unique`      (`slug`),
  UNIQUE KEY `social_groups_join_code_unique` (`join_code`),
  CONSTRAINT `social_groups_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `social_groups` (`id`,`type`,`name`,`slug`,`creator_id`,`privacy`,`join_code`,`created_at`) VALUES
  (3,'community','Cộng đồng CNTT','cong-dong-cntt',2,'public','ABCDE','2026-03-10 08:00:00');

-- ============================================================
-- Bảng: group_members
-- Mô tả: Thành viên nhóm xã hội (n-m giữa users và social_groups)
-- ============================================================
CREATE TABLE `group_members` (
  `id`        bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id`  bigint(20) UNSIGNED NOT NULL,
  `user_id`   bigint(20) UNSIGNED NOT NULL,
  `role`      enum('member','admin') NOT NULL DEFAULT 'member',
  `joined_at` timestamp           NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `group_members_group_user_unique` (`group_id`, `user_id`),
  CONSTRAINT `gm_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `social_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gm_user_id_foreign`  FOREIGN KEY (`user_id`)  REFERENCES `users`         (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: conversations
-- Mô tả: Hội thoại trực tiếp hoặc nhóm
-- Thay đổi so v1: thêm `requires_approval`
-- ============================================================
CREATE TABLE `conversations` (
  `id`                bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type`              enum('direct','group') NOT NULL DEFAULT 'direct',
  `name`              varchar(255)        DEFAULT NULL,
  `avatar_url`        varchar(255)        DEFAULT NULL,
  `theme_color`       varchar(20)         DEFAULT '#0071e3',
  `join_code`         varchar(10)         DEFAULT NULL,
  `requires_approval` tinyint(1)          NOT NULL DEFAULT 0,
  `creator_id`        bigint(20) UNSIGNED DEFAULT NULL,
  `last_message_id`   bigint(20) UNSIGNED DEFAULT NULL,
  `created_at`        timestamp           NULL DEFAULT NULL,
  `updated_at`        timestamp           NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `conversations_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: participants
-- Mô tả: Thành viên hội thoại (n-m giữa users và conversations)
-- Thay đổi so v1: thêm `status`, `is_muted`, `deleted_at`
-- ============================================================
CREATE TABLE `participants` (
  `id`              bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `user_id`         bigint(20) UNSIGNED NOT NULL,
  `role`            enum('member','admin') NOT NULL DEFAULT 'member',
  `status`          enum('active','left','removed') NOT NULL DEFAULT 'active',
  `is_muted`        tinyint(1)          NOT NULL DEFAULT 0,
  `joined_at`       timestamp           NOT NULL DEFAULT current_timestamp(),
  `deleted_at`      timestamp           NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `participants_conv_user_unique` (`conversation_id`, `user_id`),
  CONSTRAINT `participants_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `participants_user_id_foreign`         FOREIGN KEY (`user_id`)         REFERENCES `users`         (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: messages
-- Mô tả: Tin nhắn trong hội thoại
-- Thay đổi so v1: thêm `is_pinned`, `deleted_at`
-- ============================================================
CREATE TABLE `messages` (
  `id`              bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id`       bigint(20) UNSIGNED NOT NULL,
  `parent_id`       bigint(20) UNSIGNED DEFAULT NULL,
  `message_type`    enum('text','image','video','file','call_log','system') NOT NULL DEFAULT 'text',
  `content`         text                DEFAULT NULL,
  `metadata`        longtext            DEFAULT NULL,
  `is_read`         tinyint(1)          NOT NULL DEFAULT 0,
  `is_pinned`       tinyint(1)          NOT NULL DEFAULT 0,
  `created_at`      timestamp           NULL DEFAULT NULL,
  `deleted_at`      timestamp           NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_sender_id_foreign`       FOREIGN KEY (`sender_id`)       REFERENCES `users`         (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: appointments
-- Mô tả: Lịch hẹn được tạo trong hội thoại (MỚI)
-- Quan hệ: Users(1–n), Conversations(1–n)
-- ============================================================
CREATE TABLE `appointments` (
  `id`               bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id`  bigint(20) UNSIGNED NOT NULL,
  `creator_id`       bigint(20) UNSIGNED NOT NULL,
  `title`            varchar(255)        NOT NULL,
  `appointment_time` datetime            NOT NULL,
  `location`         varchar(255)        DEFAULT NULL,
  `description`      text                DEFAULT NULL,
  `status`           enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  `created_at`       timestamp           NULL DEFAULT NULL,
  `updated_at`       timestamp           NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointments_conversation_id_index` (`conversation_id`),
  KEY `appointments_creator_id_index`      (`creator_id`),
  CONSTRAINT `appointments_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `appointments_creator_id_foreign`       FOREIGN KEY (`creator_id`)       REFERENCES `users`         (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: posts
-- Mô tả: Bài đăng của người dùng
-- Thay đổi so v1: thêm `post_type`, `conversation_id`, `parent_id`;
--                 đổi tên `status` → `moderation_status`,
--                         `ai_flagged_reason` → `ai_flagged`
-- ============================================================
CREATE TABLE `posts` (
  `id`                bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`           bigint(20) UNSIGNED NOT NULL,
  `group_id`          bigint(20) UNSIGNED DEFAULT NULL,
  `conversation_id`   bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id`         bigint(20) UNSIGNED DEFAULT NULL,
  `post_type`         enum('normal','repost','shared') NOT NULL DEFAULT 'normal',
  `content`           text                NOT NULL,
  `link_url`          varchar(512)        DEFAULT NULL,
  `like_count`        int(10) UNSIGNED    DEFAULT 0,
  `reply_count`       int(10) UNSIGNED    DEFAULT 0,
  `moderation_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `ai_flagged`        varchar(255)        DEFAULT NULL,
  `created_at`        timestamp           NOT NULL DEFAULT current_timestamp(),
  `deleted_at`        timestamp           NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `posts_user_id_index`         (`user_id`),
  KEY `posts_group_id_index`        (`group_id`),
  KEY `posts_conversation_id_index` (`conversation_id`),
  CONSTRAINT `posts_user_id_foreign`         FOREIGN KEY (`user_id`)         REFERENCES `users`         (`id`) ON DELETE CASCADE,
  CONSTRAINT `posts_group_id_foreign`        FOREIGN KEY (`group_id`)        REFERENCES `social_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `posts_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: post_media
-- Mô tả: Media đính kèm bài đăng (image/video/gif/file)
-- ============================================================
CREATE TABLE `post_media` (
  `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id`    bigint(20) UNSIGNED NOT NULL,
  `media_url`  varchar(255)        NOT NULL,
  `file_name`  varchar(255)        DEFAULT NULL,
  `media_type` enum('image','video','gif','file') NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `post_media_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: comments
-- Mô tả: Bình luận bài đăng (hỗ trợ reply lồng nhau)
-- ============================================================
CREATE TABLE `comments` (
  `id`              bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`         bigint(20) UNSIGNED NOT NULL,
  `post_id`         bigint(20) UNSIGNED NOT NULL,
  `parent_id`       bigint(20) UNSIGNED DEFAULT NULL,
  `content`         text                NOT NULL,
  `image_url`       varchar(255)        DEFAULT NULL,
  `file_url`        varchar(255)        DEFAULT NULL,
  `file_name`       varchar(255)        DEFAULT NULL,
  `reply_count`     int(11)             DEFAULT 0,
  `status`          enum('pending','approved','rejected') DEFAULT 'pending',
  `ai_flagged_reason` varchar(255)      DEFAULT NULL,
  `created_at`      timestamp           NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `comments_post_id_index`   (`post_id`),
  KEY `comments_parent_id_index` (`parent_id`),
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: likes
-- Mô tả: Lượt thích (n-m giữa users và posts)
-- ============================================================
CREATE TABLE `likes` (
  `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    bigint(20) UNSIGNED NOT NULL,
  `post_id`    bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp           NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `likes_user_post_unique` (`user_id`, `post_id`),
  CONSTRAINT `likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `likes_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: reposts
-- Mô tả: Chia sẻ lại bài đăng (n-m giữa users và posts)
-- ============================================================
CREATE TABLE `reposts` (
  `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    bigint(20) UNSIGNED NOT NULL,
  `post_id`    bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp           NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reposts_user_post_unique` (`user_id`, `post_id`),
  CONSTRAINT `reposts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reposts_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: notifications
-- Mô tả: Thông báo hệ thống (like/reply/repost/follow)
-- ============================================================
CREATE TABLE `notifications` (
  `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    bigint(20) UNSIGNED NOT NULL,
  `actor_id`   bigint(20) UNSIGNED NOT NULL,
  `type`       enum('like','reply','repost','follow') NOT NULL,
  `post_id`    bigint(20) UNSIGNED DEFAULT NULL,
  `is_read`    tinyint(1)          NOT NULL DEFAULT 0,
  `created_at` timestamp           NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_index` (`user_id`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: reports
-- Mô tả: Báo cáo vi phạm (users báo cáo posts/users/comments)
-- Quan hệ: Users(1–n), liên quan đến Posts/Users/Comments(n–1)
-- MỚI hoàn toàn
-- ============================================================
CREATE TABLE `reports` (
  `id`          bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     bigint(20) UNSIGNED NOT NULL  COMMENT 'Người gửi báo cáo',
  `reported_id` bigint(20) UNSIGNED NOT NULL  COMMENT 'ID của đối tượng bị báo cáo',
  `type`        enum('post','user','comment')  NOT NULL,
  `reason`      varchar(255)        NOT NULL,
  `details`     text                DEFAULT NULL,
  `status`      enum('pending','reviewed','resolved','dismissed') NOT NULL DEFAULT 'pending',
  `created_at`  timestamp           NULL DEFAULT NULL,
  `updated_at`  timestamp           NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `reports_user_id_index`     (`user_id`),
  KEY `reports_reported_id_index` (`reported_id`),
  CONSTRAINT `reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- ============================================================
-- CÁC BẢNG CHUẨN CỦA LARAVEL FRAMEWORK
-- ============================================================

-- ============================================================
-- Bảng: password_reset_tokens
-- Mô tả: Lưu token đặt lại mật khẩu (Auth)
-- ============================================================
CREATE TABLE `password_reset_tokens` (
  `email`      varchar(100) NOT NULL,
  `token`      varchar(255) NOT NULL,
  `created_at` timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: cache
-- Mô tả: Bộ nhớ đệm ứng dụng (CACHE_DRIVER=database)
-- ============================================================
CREATE TABLE `cache` (
  `key`        varchar(255) NOT NULL,
  `value`      mediumtext   NOT NULL,
  `expiration` int(11)      NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: cache_locks
-- Mô tả: Khoá cache, tránh race condition
-- ============================================================
CREATE TABLE `cache_locks` (
  `key`        varchar(255) NOT NULL,
  `owner`      varchar(255) NOT NULL,
  `expiration` int(11)      NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: jobs
-- Mô tả: Hàng đợi tác vụ nền (QUEUE_CONNECTION=database)
-- Dùng cho: gửi email, xử lý AI moderation, push notification...
-- ============================================================
CREATE TABLE `jobs` (
  `id`           bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue`        varchar(255)        NOT NULL,
  `payload`      longtext            NOT NULL,
  `attempts`     tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `reserved_at`  int(10) UNSIGNED    DEFAULT NULL,
  `available_at` int(10) UNSIGNED    NOT NULL,
  `created_at`   int(10) UNSIGNED    NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: job_batches
-- Mô tả: Nhóm tác vụ batch (Bus::batch()) — Laravel 11+
-- ============================================================
CREATE TABLE `job_batches` (
  `id`             varchar(255) NOT NULL,
  `name`           varchar(255) NOT NULL,
  `total_jobs`     int(11)      NOT NULL,
  `pending_jobs`   int(11)      NOT NULL,
  `failed_jobs`    int(11)      NOT NULL,
  `failed_job_ids` longtext     NOT NULL,
  `options`        mediumtext   DEFAULT NULL,
  `cancelled_at`   int(11)      DEFAULT NULL,
  `created_at`     int(11)      NOT NULL,
  `finished_at`    int(11)      DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng: failed_jobs
-- Mô tả: Tác vụ thất bại, lưu để kiểm tra / retry
-- ============================================================
CREATE TABLE `failed_jobs` (
  `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid`       varchar(255)        NOT NULL,
  `connection` text                NOT NULL,
  `queue`      text                NOT NULL,
  `payload`    longtext            NOT NULL,
  `exception`  longtext            NOT NULL,
  `failed_at`  timestamp           NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- ============================================================
-- 1. Bảng: password_reset_tokens
-- Vai trò: Lưu token đặt lại mật khẩu (tương ứng OTP qua email). PK là email.
-- ============================================================
CREATE TABLE `password_reset_tokens` (
  `email`      varchar(100) NOT NULL,
  `token`      varchar(255) NOT NULL,
  `created_at` timestamp    NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. Bảng: sessions
-- Vai trò: Quản lý phiên làm việc người dùng (Laravel Session Driver = database).
-- ============================================================
CREATE TABLE `sessions` (
  `id`            varchar(255) NOT NULL,
  `user_id`       bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address`    varchar(45)  DEFAULT NULL,
  `user_agent`    text         DEFAULT NULL,
  `payload`       longtext     NOT NULL,
  `last_activity` int(11)      NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index`       (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. Bảng: cache
-- Vai trò: Bộ nhớ đệm dạng key-value (Cache Driver = database, backup cho Redis).
-- ============================================================
CREATE TABLE `cache` (
  `key`        varchar(255) NOT NULL,
  `value`      mediumtext   NOT NULL,
  `expiration` int(11)      NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. Bảng: cache_locks
-- Vai trò: Khoá phân tán (distributed lock) ngăn race condition khi cache.
-- ============================================================
CREATE TABLE `cache_locks` (
  `key`        varchar(255) NOT NULL,
  `owner`      varchar(255) NOT NULL,
  `expiration` int(11)      NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. Bảng: jobs
-- Vai trò: Hàng đợi tác vụ bất đồng bộ (Queue Driver = Redis; bảng này là fallback).
-- ============================================================
CREATE TABLE `jobs` (
  `id`           bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue`        varchar(255)        NOT NULL,
  `payload`      longtext            NOT NULL,
  `attempts`     tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `reserved_at`  int(10) UNSIGNED    DEFAULT NULL,
  `available_at` int(10) UNSIGNED    NOT NULL,
  `created_at`   int(10) UNSIGNED    NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. Bảng: job_batches
-- Vai trò: Quản lý nhóm tác vụ theo lô (Laravel Bus Batch).
-- ============================================================
CREATE TABLE `job_batches` (
  `id`             varchar(255) NOT NULL,
  `name`           varchar(255) NOT NULL,
  `total_jobs`     int(11)      NOT NULL,
  `pending_jobs`   int(11)      NOT NULL,
  `failed_jobs`    int(11)      NOT NULL,
  `failed_job_ids` longtext     NOT NULL,
  `options`        mediumtext   DEFAULT NULL,
  `cancelled_at`   int(11)      DEFAULT NULL,
  `created_at`     int(11)      NOT NULL,
  `finished_at`    int(11)      DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. Bảng: failed_jobs
-- Vai trò: Lưu các tác vụ thất bại sau nhiều lần thử lại, phục vụ giám sát.
-- ============================================================
CREATE TABLE `failed_jobs` (
  `id`         bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid`       varchar(255)        NOT NULL,
  `connection` text                NOT NULL,
  `queue`      text                NOT NULL,
  `payload`    longtext            NOT NULL,
  `exception`  longtext            NOT NULL,
  `failed_at`  timestamp           NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
-- ============================================================
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;