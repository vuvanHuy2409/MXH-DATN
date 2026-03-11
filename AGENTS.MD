# E-Connect Developer & Agent Guide 🤖

This document provides a deep-dive into the **E-Connect** project, an internal social network for East Asia University of Technology (EAUT). It is designed to help AI agents and developers understand the codebase, architecture, and styling standards perfectly.

---

## 🏛 1. Core Architecture

### 🛠 Tech Stack
- **Backend:** Laravel 12.x (PHP 8.2+)
- **Database:** MySQL (Eloquent ORM)
- **Real-time:** Laravel Reverb (WebSockets)
- **Frontend:** Blade Templates, Tailwind CSS v4, Vanilla JavaScript
- **Auth:** Custom Auth with OTP (Email-based, restricted to `@eaut.edu.vn`)

### 📁 Directory Highlights
- `app/Models/`: Eloquent models with defined relationships.
- `app/Http/Controllers/`: Request handling logic.
- `resources/views/`: Blade templates (organized by feature).
- `resources/css/app.css`: Tailwind and base styles.
- `routes/web.php`: Primary entry points.
- `routes/channels.php`: WebSocket channel authorizations.

---

## 🗄 2. Database & Models Detail

### `User.php`
- **Types:** `student` or `teacher`.
- **Relations:** 
    - `student()` -> `StudentDetail` (student_id, class, faculty_id, dob).
    - `teacher()` -> `TeacherDetail` (full_name, faculty_id).
- **Auth:** Uses `password_hash` column instead of default `password`.

### `Post.php`
- **Features:** `SoftDeletes` enabled. `UPDATED_AT` is disabled.
- **Relations:** `user`, `group`, `comments`, `media`, `likes`, `reposts`.
- **Fields:** `content`, `link_url`, `like_count`, `reply_count`.

### `Comment.php`
- **Threading:** Uses `parent_id` for nested replies.
- **Fields:** `content`, `image_url` (for file attachments).
- **Auto-loading:** Always loads `user` relationship (`protected $with = ['user']`).

### `SocialGroup.php`
- **Logic:** Automatically generates a `slug` and a unique 5-character `join_code` on creation.
- **Types:** `class` (Lớp học) or `community` (Cộng đồng).

---

## 🎨 3. UI/UX Standards (Glassmorphism)

E-Connect follows a strict **Apple-inspired Glassmorphism** design. Use the following CSS variables (defined in `layouts/app.blade.php`):

### CSS Variables (Light Mode)
```css
--bg-main: #D1E9F6;
--glass-bg: rgba(255, 255, 255, 0.75);
--glass-border: rgba(255, 255, 255, 0.5);
--glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.05);
--accent-color: #0071e3;
```

### CSS Variables (Dark Mode - `[data-theme="dark"]`)
```css
--bg-main: #0a0a0a;
--glass-bg: rgba(28, 28, 30, 0.8);
--glass-border: rgba(255, 255, 255, 0.08);
--glass-shadow: 0 12px 40px rgba(0, 0, 0, 0.6);
```

### Common Classes
- `.glass-bubble`: The primary glass container style (blur 30px, border, shadow).
- `.avatar`: Circular images with white borders.
- `.post-card`: Layout for feed items.

---

## ⚡ 4. Frontend Interactions (Vanilla JS)

Avoid heavy JS frameworks. Use the built-in utility functions in `app.blade.php`:

- `toggleLike(postId)`: Handles real-time like/unlike via `fetch()`.
- `toggleRepost(postId)`: Handles repost logic.
- `openModal() / closeModal()`: Manages the post creation modal.
- `openNotifModal()`: Fetches and displays notifications.
- `previewMedia(event)`: Handles multiple file selection previews before posting.

---

## 📡 5. Real-time & Notifications

### WebSocket Channels
- **Private:** `App.Models.User.{id}` - Used for user-specific notifications and message counts.

### Notification Flow
1. Action occurs (Like/Reply/Follow).
2. `Notification` record created in DB.
3. Broadcast event dispatched (if configured).
4. Frontend polls `/api/unread-counts` every 10 seconds (fallback) or listens via Echo.

---

## 🔐 6. Authentication Workflow

### Registration
1. User enters details (Must use `@eaut.edu.vn` prefix).
2. Data saved to `session('registration_data')`.
3. OTP generated, saved to `Cache` (1 min), and sent via Email.
4. User enters OTP -> `User` and `Detail` records created in a `DB::transaction`.

---

## 🚀 7. Development Guidelines for AI

1.  **Surgical Edits:** When modifying Blade files, preserve the complex `style` and `script` sections at the bottom/top.
2.  **Naming:** 
    - Routes: `posts.store`, `messages.show`, etc.
    - Controllers: `PostController`, `AuthController`.
3.  **Localization:** All text in Blade should use `{{ __('Text') }}` or `@lang('Text')`. Update `lang/vi.json` for translations.
4.  **Media:** Posts support multiple images/files via `PostMedia`. Always handle `enctype="multipart/form-data"` in forms.
5.  **Search First:** Use `grep_search` to find existing implementations of Modals or AJAX calls to maintain consistency.

---
© 2026 E-Connect Project. This guide is maintained for autonomous development efficiency.
