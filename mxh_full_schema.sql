DROP DATABASE IF EXISTS threads_clone;
CREATE DATABASE IF NOT EXISTS threads_clone 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE threads_clone;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;



CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `reply_count` int(11) DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `ai_flagged_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `post_id`, `parent_id`, `content`, `reply_count`, `status`, `ai_flagged_reason`, `created_at`) VALUES
(1, 2, 1, NULL, 'hello', 0, 'pending', NULL, '2026-03-09 11:14:25'),
(2, 2, 1, NULL, 'một', 0, 'pending', NULL, '2026-03-09 11:14:33'),
(3, 2, 2, NULL, 'hello', 0, 'pending', NULL, '2026-03-09 12:56:28'),
(7, 2, 7, NULL, 'chào', 0, 'pending', NULL, '2026-03-10 07:55:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `conversations`
--

CREATE TABLE `conversations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('direct','group') NOT NULL DEFAULT 'direct',
  `name` varchar(255) DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `theme_color` varchar(20) DEFAULT '#0071e3',
  `join_code` varchar(10) DEFAULT NULL,
  `creator_id` bigint(20) UNSIGNED DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 0,
  `last_message_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `faculties`
--

CREATE TABLE `faculties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `faculties`
--

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

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `follows`
--

CREATE TABLE `follows` (
  `follower_id` bigint(20) UNSIGNED NOT NULL,
  `following_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `likes`
--

CREATE TABLE `likes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `post_id`, `created_at`) VALUES
(3, 2, 1, '2026-03-09 19:38:34'),
(5, 2, 2, '2026-03-09 19:50:41'),
(11, 2, 3, '2026-03-10 08:03:09'),
(12, 2, 20, '2026-03-10 08:09:14'),
(13, 2, 21, '2026-03-10 08:25:38'),
(14, 2, 8, '2026-03-10 08:29:33'),
(15, 2, 10, '2026-03-10 08:40:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `content` text DEFAULT NULL,
  `message_type` enum('text','image','video','file','call_log','system') NOT NULL DEFAULT 'text',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `actor_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('like','reply','repost','follow') NOT NULL,
  `post_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `participants`
--

CREATE TABLE `participants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('member','admin') NOT NULL DEFAULT 'member',
  `status` enum('pending','active') NOT NULL DEFAULT 'active',
  `is_muted` tinyint(1) NOT NULL DEFAULT 0,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `social_groups`
--

CREATE TABLE `social_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('community','class') NOT NULL DEFAULT 'community',
  `name` varchar(255) NOT NULL,
  `class_name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `cover_url` varchar(255) DEFAULT NULL,
  `creator_id` bigint(20) UNSIGNED NOT NULL,
  `privacy` enum('public','private') NOT NULL DEFAULT 'public',
  `join_code` varchar(5) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `social_groups`
--

INSERT INTO `social_groups` (`id`, `type`, `name`, `class_name`, `slug`, `description`, `avatar_url`, `cover_url`, `creator_id`, `privacy`, `join_code`, `created_at`, `updated_at`) VALUES
(3, 'community', 'Cộng đồng CNTT', NULL, 'cong-dong-cntt', 'Nơi giao lưu của sinh viên CNTT', '/uploads/groups/community-default.png', NULL, 2, 'public', 'ABCDE', '2026-03-10 08:00:00', '2026-03-10 08:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `group_members`
--

CREATE TABLE `group_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('member','admin') NOT NULL DEFAULT 'member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `group_members`
--

INSERT INTO `group_members` (`id`, `group_id`, `user_id`, `role`, `joined_at`) VALUES
(1, 3, 2, 'admin', '2026-03-10 08:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `posts`
--

CREATE TABLE `posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `content` text NOT NULL,
  `link_url` varchar(512) DEFAULT NULL,
  `like_count` int(10) UNSIGNED DEFAULT 0,
  `reply_count` int(10) UNSIGNED DEFAULT 0,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `ai_flagged_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `group_id`, `content`, `link_url`, `like_count`, `reply_count`, `status`, `ai_flagged_reason`, `created_at`, `deleted_at`) VALUES
(1, 2, NULL, 'hello', NULL, 1, 2, 'pending', NULL, '2026-03-09 08:40:53', NULL),
(2, 2, NULL, 'caffee and tea', NULL, 1, 1, 'pending', NULL, '2026-03-09 12:45:15', NULL),
(3, 2, NULL, 'này', NULL, 1, 0, 'pending', NULL, '2026-03-09 20:26:57', NULL),
(4, 2, NULL, 'hôm nay', NULL, 0, 0, 'pending', NULL, '2026-03-09 20:27:07', NULL),
(5, 2, NULL, 'là', NULL, 0, 0, 'pending', NULL, '2026-03-09 20:27:13', NULL),
(6, 2, NULL, 'ngày mai', NULL, 0, 0, 'pending', NULL, '2026-03-09 20:27:38', NULL),
(7, 2, NULL, 'ngày kia', NULL, 0, 1, 'pending', NULL, '2026-03-09 20:27:45', NULL),
(8, 2, NULL, 'hello', NULL, 1, 0, 'pending', NULL, '2026-03-10 05:43:41', NULL),
(9, 2, NULL, 'helo', NULL, 0, 0, 'pending', NULL, '2026-03-10 06:38:37', NULL),
(10, 2, NULL, 'hello', NULL, 1, 0, 'pending', NULL, '2026-03-10 06:43:13', NULL),
(11, 2, NULL, 'hello', NULL, 0, 0, 'pending', NULL, '2026-03-10 06:46:34', NULL),
(12, 2, NULL, 'hello', NULL, 0, 0, 'pending', NULL, '2026-03-10 06:50:19', NULL),
(18, 2, NULL, 'hãy kết bạn với tôi', 'https://www.facebook.com', 0, 0, 'pending', NULL, '2026-03-10 07:54:06', '2026-03-10 08:38:56'),
(19, 2, NULL, 'https://www.facebook.com \r\nhãy kết bạn với tôi', NULL, 0, 0, 'pending', NULL, '2026-03-10 07:54:22', NULL),
(20, 2, NULL, 'cafe buổi sáng', NULL, 1, 0, 'pending', NULL, '2026-03-10 08:09:10', NULL),
(21, 2, NULL, 'đây là trang cá nhân của tôi : google.com hãy kết bạn với tôi', NULL, 1, 0, 'pending', NULL, '2026-03-10 08:20:19', '2026-03-10 08:38:51'),
(22, 2, 3, 'hello', NULL, 0, 0, 'pending', NULL, '2026-03-10 08:28:37', NULL),
(23, 2, NULL, 'chào anh em', NULL, 0, 0, 'pending', NULL, '2026-03-10 08:39:26', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `post_media`
--

CREATE TABLE `post_media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `media_url` varchar(255) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `media_type` enum('image','video','gif','file') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `post_media`
--

INSERT INTO `post_media` (`id`, `post_id`, `media_url`, `media_type`) VALUES
(1, 2, '/storage/posts/Qeqt33pRqYPxISgcoqmhWLWBZoylZeogkpFVqICM.gif', 'gif'),
(2, 2, '/storage/posts/NxQbj6vCYJkSmQum4LyuxfVi8H1pcqWhRjHcjptI.gif', 'gif'),
(3, 2, '/storage/posts/pw4NHfCMsSxLTS0Ccxcf8U6LRoSMPuAmTjNuoE9i.gif', 'gif'),
(4, 2, '/storage/posts/UlLbDjGRnpU6DQEoJCs6CXFQjwSNCabUEIWICpYj.gif', 'gif'),
(5, 2, '/storage/posts/KQHTdJgXqhwpbNVtNt5txLTAx5sUzGuueJBgo3i7.gif', 'gif'),
(19, 20, '/storage/posts/pdZZ77smYASgGJBab9rlKvppknubNw84iuyCU6Cp.gif', 'gif');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reposts`
--

CREATE TABLE `reposts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `student_details`
--

CREATE TABLE `student_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `dob` date DEFAULT NULL,
  `class` varchar(50) NOT NULL,
  `faculty_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `teacher_details`
--

CREATE TABLE `teacher_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `faculty_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `teacher_details`
--

INSERT INTO `teacher_details` (`id`, `user_id`, `full_name`, `faculty_id`) VALUES
(2, 2, 'Vũ Thị Thảo', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `user_type` enum('student','teacher') NOT NULL,
  `avatar_url` varchar(255) DEFAULT '/avatars/user.png',
  `bio` varchar(160) DEFAULT NULL,
  `link_url` text DEFAULT NULL,
  `follower_count` int(10) UNSIGNED DEFAULT 0,
  `following_count` int(10) UNSIGNED DEFAULT 0,
  `status` enum('active','banned') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `user_type`, `avatar_url`, `bio`, `link_url`, `follower_count`, `following_count`, `status`, `created_at`, `updated_at`) VALUES
(2, 'vuthithao', '20222591@eaut.edu.vn', '$2y$12$7gjPk8y9w6Cl5nXn0bTf8e8yJIFCXkCDcrHCb13KkkiOACn0VWDL6', 'teacher', '/avatars/1773123695_2.jpeg', '20222591 \r\nhuyberr@gmail.com\r\nhãy gọi cho tôi', 'https://www.youtube.com/watch?v=XhUXUYEwYf0&list=RD4mtpDkVUE8w&index=3', 0, 0, 'active', '2026-03-09 08:40:18', '2026-03-10 06:21:35');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_post_id_index` (`post_id`),
  ADD KEY `comments_parent_id_index` (`parent_id`),
  ADD KEY `comments_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `conversations_join_code_unique` (`join_code`),
  ADD KEY `conversations_creator_id_foreign` (`creator_id`);

--
-- Chỉ mục cho bảng `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `follows`
--
ALTER TABLE `follows`
  ADD PRIMARY KEY (`follower_id`,`following_id`),
  ADD KEY `follows_following_id_foreign` (`following_id`);

--
-- Chỉ mục cho bảng `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `likes_user_post_unique` (`user_id`,`post_id`),
  ADD KEY `likes_post_id_foreign` (`post_id`);

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_conversation_id_foreign` (`conversation_id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `participants_conv_user_unique` (`conversation_id`,`user_id`),
  ADD KEY `participants_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `social_groups`
--
ALTER TABLE `social_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `social_groups_slug_unique` (`slug`),
  ADD UNIQUE KEY `social_groups_join_code_unique` (`join_code`),
  ADD KEY `social_groups_creator_id_foreign` (`creator_id`);

--
-- Chỉ mục cho bảng `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_members_group_id_user_id_unique` (`group_id`,`user_id`),
  ADD KEY `group_members_user_id_foreign` (`user_id`);

--
-- Chỉ mục cho bảng `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posts_user_id_foreign` (`user_id`),
  ADD KEY `posts_group_id_foreign` (`group_id`);

--
-- Chỉ mục cho bảng `post_media`
--
ALTER TABLE `post_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_media_post_id_foreign` (`post_id`);

--
-- Chỉ mục cho bảng `reposts`
--
ALTER TABLE `reposts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reposts_user_post_unique` (`user_id`,`post_id`),
  ADD KEY `reposts_post_id_foreign` (`post_id`);

--
-- Chỉ mục cho bảng `student_details`
--
ALTER TABLE `student_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sd_student_id_unique` (`student_id`),
  ADD KEY `sd_user_id_foreign` (`user_id`),
  ADD KEY `sd_faculty_id_foreign` (`faculty_id`);

--
-- Chỉ mục cho bảng `teacher_details`
--
ALTER TABLE `teacher_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `td_user_id_foreign` (`user_id`),
  ADD KEY `td_faculty_id_foreign` (`faculty_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `faculties`
--
ALTER TABLE `faculties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `likes`
--
ALTER TABLE `likes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `participants`
--
ALTER TABLE `participants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `social_groups`
--
ALTER TABLE `social_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `group_members`
--
ALTER TABLE `group_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT cho bảng `post_media`
--
ALTER TABLE `post_media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `reposts`
--
ALTER TABLE `reposts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `student_details`
--
ALTER TABLE `student_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `teacher_details`
--
ALTER TABLE `teacher_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `follows`
--
ALTER TABLE `follows`
  ADD CONSTRAINT `follows_follower_id_foreign` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `follows_following_id_foreign` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `participants_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `social_groups`
--
ALTER TABLE `social_groups`
  ADD CONSTRAINT `social_groups_creator_id_foreign` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `social_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `social_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `post_media`
--
ALTER TABLE `post_media`
  ADD CONSTRAINT `post_media_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reposts`
--
ALTER TABLE `reposts`
  ADD CONSTRAINT `reposts_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reposts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `student_details`
--
ALTER TABLE `student_details`
  ADD CONSTRAINT `sd_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sd_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `teacher_details`
--
ALTER TABLE `teacher_details`
  ADD CONSTRAINT `td_faculty_id_foreign` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `td_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;
