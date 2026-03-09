@extends('layouts.app')

@section('content')
<div class="profile-container" style="position: relative; padding-bottom: 30px;">
    <!-- Large Cover Image -->
    <div class="profile-cover">
        <div class="cover-pattern"></div>
    </div>

    <!-- Floating Profile Card -->
    <div class="profile-info-card glass-bubble" style="margin: -80px 20px 0; padding: 25px; position: relative; z-index: 10; border-radius: 35px; box-shadow: 0 20px 50px rgba(0,0,0,0.15);">
        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
            <!-- Avatar overlapping -->
            <div class="avatar-container" style="position: relative; margin-top: -75px;">
                <div class="avatar" style="width: 130px; height: 130px; background-image: url('{{ $user->avatar_url }}'); background-size: cover; border: 6px solid var(--glass-bg); box-shadow: 0 10px 30px rgba(0,0,0,0.12); border-radius: 40px !important;"></div>
                @if($user->user_type === 'teacher')
                <div title="Giảng viên xác minh" style="position: absolute; bottom: 8px; right: 8px; background: #FFD700; color: #000; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid var(--glass-bg); font-weight: 900; font-size: 14px;">✓</div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 10px; margin-bottom: 5px;">
                @if(auth()->id() === $user->id)
                <a href="{{ route('profile.edit') }}" class="glass-btn" style="padding: 10px 20px; border-radius: 18px; text-decoration: none; color: var(--text-color); font-weight: 700; font-size: 14px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.2); transition: all 0.3s;">
                    Chỉnh sửa
                </a>
                @else
                <form action="{{ route('users.follow', $user) }}" method="POST">
                    @csrf
                    <button type="submit" style="background: var(--text-color); color: var(--bg-main); border: none; padding: 10px 25px; border-radius: 18px; font-weight: 700; font-size: 14px; cursor: pointer; transition: transform 0.2s;">
                        {{ auth()->user()->following->contains($user->id) ? 'Đang theo dõi' : 'Theo dõi' }}
                    </button>
                </form>
                <button onclick="handleMessageClick({{ auth()->user()->following->contains($user->id) && $user->following->contains(auth()->id()) ? 'true' : 'false' }}, '{{ route('messages.direct', $user->id) }}')" class="glass-btn" style="width: 42px; height: 42px; border-radius: 15px; border: 1px solid var(--glass-border); background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; cursor: pointer;">
                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                    </svg>
                </button>
                @endif
            </div>
        </div>

        <div style="margin-top: 15px;">
            <h1 style="margin: 0; font-size: 26px; font-weight: 800; letter-spacing: -0.8px;">{{ $user->full_name ?? $user->username }}</h1>
            <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px; color: var(--secondary-text); font-weight: 500;">
                <span>@<span>{{ $user->username }}</span></span>
                @if($user->user_type === 'teacher')
                <span class="badge-new">Giáo viên</span>
                @else
                <span class="badge-new">{{ $user->student->class ?? 'Sinh viên' }}</span>
                @endif
            </div>

            <!-- Academic Info Section -->
            <div style="display: flex; gap: 15px; margin-top: 12px; flex-wrap: wrap; opacity: 0.85;">
                @php
                $details = $user->user_type === 'teacher' ? $user->teacher : $user->student;
                $facultyName = $details->faculty->name ?? null;
                $class = $user->user_type === 'student' ? ($details->class ?? null) : null;
                $dob = $user->user_type === 'student' ? ($details->dob ? \Carbon\Carbon::parse($details->dob)->format('d/m/Y') : null) : null;
                @endphp

                @if($facultyName)
                <div title="Khoa" style="font-size: 13px; color: var(--secondary-text); display: flex; align-items: center; gap: 5px;">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    <span>{{ $facultyName }}</span>
                </div>
                @endif

                @if($class)
                <div title="Lớp" style="font-size: 13px; color: var(--secondary-text); display: flex; align-items: center; gap: 5px;">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="3" x2="9" y2="21"></line>
                    </svg>
                    <span>Lớp: {{ $class }}</span>
                </div>
                @endif

                @if($dob)
                <div title="Ngày sinh" style="font-size: 13px; color: var(--secondary-text); display: flex; align-items: center; gap: 5px;">
                    <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>{{ $dob }}</span>
                </div>
                @endif
            </div>
        </div>

        <p style="margin: 15px 0; font-size: 15px; line-height: 1.6; opacity: 0.9;">
            {{ $user->bio ?? 'Không có tiểu sử.' }}
        </p>

        @if($user->link_url)
        <a href="{{ $user->link_url }}" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; color: var(--accent-color); text-decoration: none; font-size: 14px; font-weight: 600; margin-bottom: 15px;">
            <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="2.5" fill="none">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
            </svg>
            {{ preg_replace('(^https?://)', '', $user->link_url) }}
        </a>
        @endif

        <div style="display: flex; gap: 25px; padding-top: 15px; border-top: 1px solid var(--glass-border);">
            <div onclick="openFollowModal('followers')" style="cursor: pointer;">
                <span style="font-weight: 800; font-size: 16px;">{{ $user->followers_count }}</span>
                <span style="font-size: 14px; color: var(--secondary-text); margin-left: 4px;">Người theo dõi</span>
            </div>
            <div onclick="openFollowModal('following')" style="cursor: pointer;">
                <span style="font-weight: 800; font-size: 16px;">{{ $user->following_count }}</span>
                <span style="font-size: 14px; color: var(--secondary-text); margin-left: 4px;">Đang theo dõi</span>
            </div>
        </div>
    </div>

    <!-- Minimalist Tabs -->
    <div style="margin: 30px 20px 10px; display: flex; gap: 30px; border-bottom: 1px solid var(--glass-border); padding: 0 10px;">
        <div id="tab-posts" style="padding: 12px 5px; cursor: default; color: var(--text-color); font-weight: 800; position: relative; font-size: 15px;">
            Bài Viết
            <div class="active-line" style="position: absolute; bottom: -1px; left: 0; width: 100%; height: 3px; background: var(--accent-color); border-radius: 3px;"></div>
        </div>
    </div>

    <!-- Content Area -->
    <div style="padding: 0 10px;">
        <div id="content-posts" class="tab-content-area" style="animation: slideIn 0.4s ease-out;">
            @forelse($posts as $post)
            @include('posts._item', ['post' => $post, 'prefix' => 'v2-p'])
            @empty
            <div style="text-align: center; padding: 60px 20px; opacity: 0.5;">Không có bài đăng nào.</div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .profile-cover {
        height: 220px;
        width: 100%;
        background: linear-gradient(to bottom right, #4facfe 0%, #00f2fe 100%);
        position: relative;
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    }

    [data-theme="dark"] .profile-cover {
        background: linear-gradient(to bottom right, #243949 0%, #517fa4 100%);
        filter: brightness(0.8);
    }

    .cover-pattern {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: radial-gradient(circle at 2px 2px, rgba(255, 255, 255, 0.15) 1px, transparent 0);
        background-size: 24px 24px;
    }

    .badge-new {
        background: rgba(0, 113, 227, 0.08);
        color: var(--accent-color);
        padding: 4px 10px;
        border-radius: 10px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .glass-btn:hover {
        background: rgba(255, 255, 255, 0.4) !important;
        transform: translateY(-2px);
    }

    .active-line {
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Override Avatar shape for this style */
    .avatar-container .avatar {
        border-radius: 35px !important;
    }
</style>

<script>
    const profileUserId = {
        {
            $user - > id
        }
    };

    function handleMessageClick(isFriend, url) {
        if (isFriend) {
            window.location.href = url;
        } else {
            alert('Bạn chỉ có thể nhắn tin khi cả hai theo dõi nhau (Bạn bè).');
        }
    }

    function openFollowModal(type) {
        const modal = document.getElementById('followModal');
        const list = document.getElementById('followList');
        const title = document.getElementById('followModalTitle');

        if (!modal || !list || !title) return;

        title.innerText = type === 'followers' ? 'Người theo dõi' : 'Đang theo dõi';
        list.innerHTML = '<div style="text-align:center; padding: 20px;">Đang tải...</div>';
        modal.style.display = 'flex';
        document.body.classList.add('modal-open');

        fetch(`/api/users/${profileUserId}/${type}`)
            .then(res => res.json())
            .then(users => {
                if (users.length === 0) {
                    list.innerHTML = '<div style="text-align:center; padding: 40px; opacity: 0.5;">Danh sách trống.</div>';
                    return;
                }
                list.innerHTML = '';
                users.forEach(u => {
                    const item = document.createElement('div');
                    item.style.cssText = 'display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 0.5px solid var(--glass-border);';
                    item.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <a href="/@${u.username}"><div class="avatar" style="width: 45px; height: 45px; border-radius: 15px !important; background-image: url('${u.avatar_url}'); background-size: cover;"></div></a>
                            <div>
                                <a href="/@${u.username}" style="text-decoration: none; color: var(--text-color); font-weight: 700; font-size: 15px;">${u.username}</a>
                                <div style="font-size: 12px; color: var(--secondary-text);">${u.followers_count} followers</div>
                            </div>
                        </div>
                        <a href="/@${u.username}" style="text-decoration: none; font-size: 12px; font-weight: 800; color: var(--accent-color);">Xem</a>
                    `;
                    list.appendChild(item);
                });
            })
            .catch(err => {
                console.error(err);
                list.innerHTML = '<div style="text-align:center; padding: 20px; color: red;">Có lỗi xảy ra khi tải dữ liệu.</div>';
            });
    }

    function closeFollowModal() {
        const modal = document.getElementById('followModal');
        if (modal) modal.style.display = 'none';
        document.body.classList.remove('modal-open');
    }
</script>

<!-- Shared Modal (Nếu layout chưa có) -->
<div id="followModal" class="modal" onclick="if(event.target === this) closeFollowModal()">
    <div class="modal-content glass-bubble" style="max-width: 450px; padding: 0; overflow: hidden; border-radius: 30px;">
        <div style="padding: 20px; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
            <h3 id="followModalTitle" style="margin: 0; font-size: 18px; font-weight: 800;">Danh sách</h3>
            <span onclick="closeFollowModal()" style="cursor: pointer; opacity: 0.5; font-size: 24px;">&times;</span>
        </div>
        <div id="followList" style="max-height: 400px; overflow-y: auto; padding: 0 20px;"></div>
    </div>
</div>
@endsection