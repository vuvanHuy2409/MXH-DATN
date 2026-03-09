DROP DATABASE IF EXISTS threads_clone;
CREATE DATABASE IF NOT EXISTS threads_clone 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE threads_clone;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


-- 1. Bảng Khoa (Faculties)
CREATE TABLE IF NOT EXISTS `faculties` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng Người dùng chung (Authentication)
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `user_type` enum('student','teacher') NOT NULL,
  `avatar_url` varchar(255) DEFAULT '/avatars/user.png',
  `bio` varchar(160) DEFAULT NULL,
  `link_url` varchar(512) DEFAULT NULL,
  `follower_count` int(10) UNSIGNED DEFAULT 0,
  `following_count` int(10) UNSIGNED DEFAULT 0,
  `status` enum('active', 'banned') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng Chi tiết Sinh viên
CREATE TABLE IF NOT EXISTS `student_details` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `dob` date DEFAULT NULL,
  `class` varchar(50) NOT NULL,
  `faculty_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sd_student_id_unique` (`student_id`),
  CONSTRAINT `sd_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sd_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng Chi tiết Giáo viên (ĐÃ SỬA LỖI CÚ PHÁP)
CREATE TABLE IF NOT EXISTS `teacher_details` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `faculty_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `td_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `td_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng Bài viết (ĐÃ THÊM TRƯỜNG AI MODERATION)
CREATE TABLE IF NOT EXISTS `posts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `link_url` varchar(512) DEFAULT NULL,
  `like_count` int(10) UNSIGNED DEFAULT 0,
  `reply_count` int(10) UNSIGNED DEFAULT 0,
  `status` enum('pending', 'approved', 'rejected') DEFAULT 'pending', 
  `ai_flagged_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bảng Bình luận (ĐÃ THÊM TRƯỜNG AI MODERATION)
CREATE TABLE IF NOT EXISTS `comments` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `content` text NOT NULL,
  `reply_count` int(11) DEFAULT 0,
  `status` enum('pending', 'approved', 'rejected') DEFAULT 'pending',
  `ai_flagged_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `comments_post_id_index` (`post_id`),
  KEY `comments_parent_id_index` (`parent_id`),
  CONSTRAINT `comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Bảng Media bài viết
CREATE TABLE IF NOT EXISTS `post_media` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `media_url` varchar(255) NOT NULL,
  `media_type` enum('image','video','gif') NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `post_media_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Bảng Thích bài viết
CREATE TABLE IF NOT EXISTS `likes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `likes_user_post_unique` (`user_id`,`post_id`),
  CONSTRAINT `likes_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Bảng Đăng lại
CREATE TABLE IF NOT EXISTS `reposts` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reposts_user_post_unique` (`user_id`,`post_id`),
  CONSTRAINT `reposts_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reposts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Bảng Theo dõi
CREATE TABLE IF NOT EXISTS `follows` (
  `follower_id` bigint(20) UNSIGNED NOT NULL,
  `following_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`follower_id`,`following_id`),
  CONSTRAINT `follows_follower_id_foreign` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `follows_following_id_foreign` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Bảng Cuộc hội thoại (Chat)
CREATE TABLE IF NOT EXISTS `conversations` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` enum('direct','group') NOT NULL DEFAULT 'direct',
  `name` varchar(255) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `theme_color` varchar(20) DEFAULT '#0071e3',
  `join_code` varchar(10) DEFAULT NULL,
  `creator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 0,
  `last_message_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `conversations_join_code_unique` (`join_code`),
  CONSTRAINT `conversations_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Bảng Thành viên hội thoại
CREATE TABLE IF NOT EXISTS `participants` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('member','admin') NOT NULL DEFAULT 'member',
  `status` enum('pending','active') NOT NULL DEFAULT 'active',
  `is_muted` tinyint(1) NOT NULL DEFAULT 0,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `participants_conv_user_unique` (`conversation_id`,`user_id`),
  CONSTRAINT `participants_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Bảng Tin nhắn
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `content` text DEFAULT NULL,
  `message_type` enum('text','image','video','file','call_log','system') NOT NULL DEFAULT 'text',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Bảng Thông báo
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `actor_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('like','reply','repost','follow') NOT NULL,
  `post_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
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
